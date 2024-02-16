<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Subtotal;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // dd(Subtotal::paginate(5));

        // 一つの購入（ユーザー一人がいくつかの商品×個数）の合計
        $totals = Subtotal::groupBy('id')
            ->selectRaw('
                id,
                sum(subtotal) as total,
                customer_name,
                status,
                created_at
            ')
            ->paginate(10);

        // dd($totals);

        return Inertia::render('Purchases/Index',
            [
                'totals' => $totals
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::select('id', 'name', 'kana')->get();
        $items = Item::select('id', 'name', 'price')->where('is_selling', true)->get();

        return Inertia::render('Purchases/Create', [
            'customers' => $customers,
            'items' => $items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request)
    {
        DB::beginTransaction();

        try {
            $purchase = Purchase::create([
                'customer_id' => $request->customer_id,
                'status' => $request->status
            ]);
    
            foreach($request->items as $item) {
                $purchase->items()->attach($purchase->id, [ // attachを用いると中間テーブルitems_purchasesにもデータを登録
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity']
                ]);
            }

            DB::commit();

            return to_route('dashboard');

        } catch(\Exception $e) {
            DB::rollback();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        //　小計
        $subtotals = Subtotal::where('id', $purchase->id)->get();

        // 合計
        $total = Subtotal::groupBy('id')
        ->where('id', $purchase->id)
            ->selectRaw('
                id,
                sum(subtotal) as total,
                customer_name,
                status,
                created_at
            ')
            ->get();

        // dd($subtotals, $total);

        return Inertia::render('Purchases/Show', [
            'subtotals' => $subtotals,
            'total' => $total
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        // $purchase = Purchase::find($purchase->id);
        // dd($purchase);
        // dd($purchase->items);

        $items = Item::select('id', 'name', 'price')->get();
        
        $targetItems = [];

        foreach($items as $item) {
            $quantity = 0;
            foreach($purchase->items as $purchaseItem) {
                if ($item->id === $purchaseItem->id) {
                    $quantity = $purchaseItem->pivot->quantity;
                }
            }
            array_push($targetItems,
                [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'quantity' => $quantity
                ]
            );
        }

        $total = Subtotal::groupBy('id')
        ->where('id', $purchase->id)
            ->selectRaw('
                id,
                customer_id,
                customer_name,
                status,
                created_at
            ')
            ->get();
        
        return Inertia::render('Purchases/Edit', [
            'items' => $targetItems,
            'total' => $total
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        // dd($request);
        // dd($purchase);

        DB::beginTransaction();

        try {
            $purchase->status = $request->status;

            $purchase->save();

            $items = [];

            foreach($request->items as $request_item) {
                $items = $items + [
                    $request_item['id'] => [
                        'quantity' => $request_item['quantity']
                    ]
                ];
            }
            // dd($items);

            $purchase->items()->sync($items);

            DB::commit();

            return to_route('dashboard');

        } catch (\Exception $e) {
            DB::rollback();
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}
