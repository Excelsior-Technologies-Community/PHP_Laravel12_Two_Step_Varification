<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
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
        ];
    }
    public function generateTwoFactorCode()
{
    $this->timestamps = false; 
    $this->two_factor_code = rand(100000, 999999);
    
    // AHIA BHUL THI $table LAKHYU HASE, TENE $this KARI NAKHO
    $this->two_factor_expires_at = now()->addMinutes(10); 
    
    $this->save();
}

public function resetTwoFactorCode()
{
    $this->timestamps = false;
    $this->two_factor_code = null;
    
    // AHIA PAN $this KARI NAKHO
    $this->two_factor_expires_at = null; 
    
    $this->save();
}
}
