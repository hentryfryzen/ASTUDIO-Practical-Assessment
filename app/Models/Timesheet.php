<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    protected $fillable = ['user_id', 'project_id', 'task_name', 'date', 'hours'];

        // Timesheet belongs to a single user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
     // Timesheet belongs to a single project
     public function project()
     {
         return $this->belongsTo(Project::class);
     }
}
