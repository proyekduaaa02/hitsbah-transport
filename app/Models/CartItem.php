<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $table = 'cart_item';

    protected $primaryKey = 'id_cart_item';

    protected $fillable = ['id_cart', 'id_produk', 'jumlah'];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class, 'id_cart', 'id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->produk->harga * $this->jumlah;
    }
}
