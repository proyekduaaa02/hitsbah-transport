<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $table = 'reviews';

    protected $primaryKey = 'id_reviews';

    protected $fillable = ['id_user', 'id_produk', 'rating', 'komen', 'balasan_admin', 'tanggal_balasan'];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'tanggal_balasan' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
