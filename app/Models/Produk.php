<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    protected $table = 'produk';

    protected $primaryKey = 'id_produk';

    protected $fillable = ['id_kategori', 'nama', 'deskripsi', 'harga', 'stok', 'gambar'];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'stok' => 'integer',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_produk', 'id_produk');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'id_produk', 'id_produk');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'id_produk', 'id_produk');
    }

    /** URL gambar produk, atau null jika belum ada (view menampilkan placeholder). */
    public function getGambarUrlAttribute(): ?string
    {
        return $this->gambar ? asset('storage/'.$this->gambar) : null;
    }

    /** Rating rata-rata; memakai withAvg() bila tersedia agar tidak N+1. */
    public function getRatingRataAttribute(): float
    {
        $avg = $this->attributes['reviews_avg_rating'] ?? null;

        return round((float) ($avg ?? $this->reviews()->avg('rating')), 1);
    }

    public function getJumlahUlasanAttribute(): int
    {
        return (int) ($this->attributes['reviews_count'] ?? $this->reviews()->count());
    }

    public function scopeWithRating(Builder $query): Builder
    {
        return $query->withAvg('reviews', 'rating')->withCount('reviews');
    }

    public function scopeSearch(Builder $query, ?string $kata): Builder
    {
        return $query->when($kata, fn ($q) => $q->where('nama', 'like', "%{$kata}%"));
    }

    public function scopeUrut(Builder $query, ?string $urutan): Builder
    {
        return match ($urutan) {
            'harga_asc' => $query->orderBy('harga'),
            'harga_desc' => $query->orderByDesc('harga'),
            default => $query->latest('id_produk'),
        };
    }
}
