<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    /** @var array<int,string> */
    protected $fillable = [
        'supplier_id',
        'invoice_no',
        'purchase_date',
        'payment_status',
        'payment_method',
        'total_amount',
        'paid_amount',
        'due_amount',
        'note',
    ];

    /* --------------------------------- relationships -------------------------------- */

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
