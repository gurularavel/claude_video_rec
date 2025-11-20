<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_operator',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_operator' => 'boolean',
        ];
    }

    public function supportSessions()
    {
        return $this->hasMany(SupportSession::class, 'operator_id');
    }

    public function acceptedSessions()
    {
        return $this->hasMany(SupportSession::class, 'accepted_by');
    }

    public function isAvailable(): bool
    {
        return $this->is_operator && $this->status === 'available';
    }

    public function isBusy(): bool
    {
        return $this->is_operator && $this->status === 'busy';
    }

    public function scopeOperators($query)
    {
        return $query->where('is_operator', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_operator', true)->where('status', 'available');
    }
}
