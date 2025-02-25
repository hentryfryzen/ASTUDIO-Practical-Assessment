<?php

namespace App\Models;


use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable {

    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['first_name', 'last_name', 'email', 'password'];

    // public function projects() {
    //     return $this->belongsToMany(Project::class)->withTimestamps();
    // }
    public function projects()
    {
        return $this->belongsToMany(Project::class)->using(ProjectUser::class);
    }
    public function timesheets() {
        return $this->hasMany(Timesheet::class);
    }
}
