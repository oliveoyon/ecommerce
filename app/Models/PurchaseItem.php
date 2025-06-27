<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    /** @var array<int,string> */
    protected $fillable = [
        'purchase_id',
        'product_id',
        'product_variant_id',
        'batch_number',
        'purchase_price',
        'quantity',
        'expiry_date',
        'total_price',
    ];

    /* --------------------------------- relationships -------------------------------- */

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
