<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'url', 'reward',
    ];

    /**
     * Define the relationship with the User model.
     * This indicates that a Task can be completed by multiple Users.
     * The 'user_task' table is used as the intermediate table for the many-to-many relationship.
     * The 'withTimestamps' method automatically manages the created_at and updated_at timestamps on the pivot table.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_tasks')->withTimestamps();
    }
}
