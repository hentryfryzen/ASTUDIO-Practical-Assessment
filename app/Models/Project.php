<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'status'];

    // Many-to-many relationship with User, using custom pivot model
    public function users() {
        // return $this->belongsToMany(User::class)->using(ProjectUser::class)->withTimestamps();
        return $this->belongsToMany(User::class, 'project_user');
    }
    // public function users()
    // {
    //     return $this->belongsToMany(User::class);
    // }

     // Project can have many timesheets
    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }

    // Polymorphic relationship with AttributeValue
    public function attributes() {
        return $this->morphMany(AttributeValue::class, 'entity');
    }

    // Another way to define the relationship for attribute values
    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class, 'entity_id');
    }

    // Helper method to get a project by its ID
    public static function getById($id)
    {
        return self::find($id);
    }
}
