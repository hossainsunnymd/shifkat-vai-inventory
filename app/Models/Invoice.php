<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = ['total', 'discount', 'vat', 'payable','payment','user_id', 'customer_id'];
    protected $attributes = ['payment' => '0'];

    function customer():BelongsTo{
        return $this->belongsTo(Customer::class);
    }

    function invoiceProduct(){
        return $this->hasMany(InvoiceProduct::class);
    }

}
