<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'kana',
        'tel',
        'email',
        'postcode',
        'address',
        'birthday',
        'gender',
        'memo',
    ];

    /**
     * カナまたは電話番号で部分一致検索
     */
    public function scopeSearchCustomers($query, $input = null)
    {
        if (!empty($input)) {
            if (Customer::
                where('kana', 'like', '%' . $input . '%') // 部分一致
                ->orWhere('tel', 'like', '%' . $input . '%')
                ->exists()
            ) { 
                return $query
                        ->where('kana', 'like', '%' . $input . '%') 
                        ->orWhere('tel', 'like', '%' . $input . '%');
            }
        }
    }
}
