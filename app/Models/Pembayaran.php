<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    public const METODE = [
        'qris' => 'QRIS',
        'dana' => 'DANA',
        'ovo' => 'OVO',
        'gopay' => 'GoPay',
        'tunai' => 'Bayar di Tempat (Tunai)',
    ];

    protected $table = 'pembayaran';

    protected $primaryKey = 'id_pembayaran';

    protected $fillable = ['id_order', 'metode', 'total_pembayaran', 'status', 'bukti_pembayaran', 'tanggal_bayar', 'catatan'];

    protected function casts(): array
    {
        return [
            'total_pembayaran' => 'decimal:2',
            'tanggal_bayar' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }

    public function getMetodeLabelAttribute(): string
    {
        return self::METODE[$this->metode] ?? strtoupper($this->metode);
    }

    public function getBuktiUrlAttribute(): ?string
    {
        return $this->bukti_pembayaran ? asset('storage/'.$this->bukti_pembayaran) : null;
    }
}
