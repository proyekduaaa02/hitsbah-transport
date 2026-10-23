<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceBooking extends Model
{
    protected $table = 'services_booking';

    protected $primaryKey = 'id_services_booking';

    protected $fillable = ['id_user', 'id_order', 'id_services', 'jadwal_pemasangan', 'status_pemasangan', 'harga'];

    protected function casts(): array
    {
        return [
            'jadwal_pemasangan' => 'datetime',
            'harga' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(InstallationService::class, 'id_services', 'id_services');
    }

    /** Booking yang masih memakai slot jadwal. */
    public function scopeMemakaiSlot(Builder $query): Builder
    {
        return $query->where('status_pemasangan', '!=', 'dibatalkan');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereIn('status_pemasangan', ['menunggu', 'dijadwalkan', 'diproses']);
    }
}
