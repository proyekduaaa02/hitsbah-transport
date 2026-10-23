<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstallationService extends Model
{
    protected $table = 'installation_services';

    protected $primaryKey = 'id_services';

    protected $fillable = ['nama', 'deskripsi', 'harga', 'durasi_pemasangan'];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'durasi_pemasangan' => 'integer',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class, 'id_services', 'id_services');
    }

    /** Contoh: "90 menit" atau "2 jam 30 menit" */
    public function getDurasiLabelAttribute(): string
    {
        $menit = $this->durasi_pemasangan;
        if ($menit < 60) {
            return $menit.' menit';
        }
        $jam = intdiv($menit, 60);
        $sisa = $menit % 60;

        return $sisa ? "{$jam} jam {$sisa} menit" : "{$jam} jam";
    }
}
