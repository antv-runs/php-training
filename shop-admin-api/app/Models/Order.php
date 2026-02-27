<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = ['user_id', 'total_amount', 'status'];

    /**
     * Items belonging to the order
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
