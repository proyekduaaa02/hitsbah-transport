<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_SUPER_ADMIN = 'super_admin';

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    /** `role` sengaja tidak masuk $fillable (mass assignment protection). */
    protected $fillable = ['nama', 'email', 'password', 'alamat'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class, 'id_user', 'id_user');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'id_user', 'id_user');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'id_user', 'id_user');
    }

    public function serviceBookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class, 'id_user', 'id_user');
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Admin',
            default => 'Pelanggan',
        };
    }

    public function dashboardUrl(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => route('superadmin.dashboard'),
            self::ROLE_ADMIN => route('admin.dashboard'),
            default => route('user.dashboard'),
        };
    }

    /** Ambil (atau buat) keranjang milik user. */
    public function getOrCreateCart(): Cart
    {
        return $this->cart()->firstOrCreate([]);
    }

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeSearch($query, ?string $kata)
    {
        return $query->when($kata, fn ($q) => $q->where(function ($q) use ($kata) {
            $q->where('nama', 'like', "%{$kata}%")->orWhere('email', 'like', "%{$kata}%");
        }));
    }
}
