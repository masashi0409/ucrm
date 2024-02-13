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
        dd($purchase);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}
