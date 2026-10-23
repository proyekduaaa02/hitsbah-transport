<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_DITERIMA = 'diterima';
    public const STATUS_DIPROSES = 'diproses';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DITOLAK = 'ditolak';

    protected $table = 'orders';

    protected $primaryKey = 'id_order';

    protected $fillable = ['id_user', 'tanggal_order', 'total_pembayaran', 'status'];

    protected function casts(): array
    {
        return [
            'tanggal_order' => 'datetime',
            'total_pembayaran' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_order', 'id_order');
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'id_order', 'id_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class, 'id_order', 'id_order');
    }

    /** Nomor pesanan yang mudah dibaca, contoh: HT-000123 */
    public function getKodeAttribute(): string
    {
        return 'HT-'.str_pad((string) $this->id_order, 6, '0', STR_PAD_LEFT);
    }

    public function getStatusPembayaranAttribute(): string
    {
        return $this->pembayaran?->status ?? 'belum';
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $query->when($status, fn ($q) => $q->where('status', $status));
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_DITERIMA, self::STATUS_DIPROSES]);
    }

    public function scopeSearch(Builder $query, ?string $kata): Builder
    {
        if (! $kata) {
            return $query;
        }

        $id = (int) preg_replace('/\D/', '', $kata);

        return $query->where(function ($q) use ($kata, $id) {
            $q->whereHas('user', fn ($u) => $u->where('nama', 'like', "%{$kata}%")->orWhere('email', 'like', "%{$kata}%"));
            if ($id > 0) {
                $q->orWhere('id_order', $id);
            }
        });
    }

    public function scopeTanggal(Builder $query, ?string $dari, ?string $sampai): Builder
    {
        return $query
            ->when($dari, fn ($q) => $q->whereDate('tanggal_order', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('tanggal_order', '<=', $sampai));
    }
}
