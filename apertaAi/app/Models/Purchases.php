<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchases extends Model
{
    use \Illuminate\Database\Eloquent\Concerns\HasUuids;
    use \Illuminate\Database\Eloquent\Concerns\HasTimestamps;

    protected $table = 'purchases';

    protected $fillable = [
        'id',
        'payment_date',
        'item',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
        'purchase_location',
        'user_id'
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'payment_date' => 'date:Y-m-d',
        'item' => 'string',
        'quantity' => 'integer',
        'unit' => 'string',
        'purchase_location' => 'string',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
