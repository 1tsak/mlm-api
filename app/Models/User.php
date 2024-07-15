<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'mobile_number', 'password', 'sponsor_id', 'referer_id', 'dob', 'address', 'balance'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->sponsor_id = self::generateSponsorId();
        });
    }

    private static function generateSponsorId()
    {
        return strtoupper(bin2hex(random_bytes(4)));
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referer_id', 'sponsor_id');
    }

    public function getAllReferrals()
    {
        $referrals = collect([]);
        $queue = collect([$this]);
        $visited = collect([$this->id]);

        while ($queue->isNotEmpty()) {
            $current = $queue->shift();
            $currentReferrals = $current->referrals;

            foreach ($currentReferrals as $referral) {
                if (!$visited->contains($referral->id)) {
                    $referrals->push($referral);
                    $queue->push($referral);
                    $visited->push($referral->id);
                }
            }
        }

        return $referrals;
    }

    public function earnings()
    {
        return $this->hasMany(Earning::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'user_tasks')->withTimestamps();
    }
    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class);
    }
}
