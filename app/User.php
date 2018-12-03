<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'id', 'first_name', 'last_name', 'email', 'interests', 'password', 'profile_image', 'status'
    ];

    protected $appends = [
        'full_name', 'role_description'
    ];

    protected $hidden = ['remember_token'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_user', 'user_id', 'role_id');
    }

    public function godsons()
    {
        return $this->belongsToMany(Godson::class)->withTimestamps();
    }

    public function issuingThreads()
    {
        return $this->hasMany(Thread::class, 'user_id_issuing', 'id');
    }

    public function receiverThreads()
    {
        return $this->hasMany(Thread::class, 'user_id_receiver', 'id');
    }

    public function notification(){
        return $this->hasOne(Notification::class, 'user_id', 'id')
            ->orderBy('id', 'desc');
    }

    public function scopeGodfathers($query)
    {
        return $query->whereHas('roles', function ($q) {
            return $q->where('description', 'Padrino');
        })->where('status', 1)->orderBy('id', 'asc');
    }

    public function scopeActive($query){
       return $query->whereStatus(1);
    }

    public function getRoleDescriptionAttribute(){
        return $this->roles()->first() !== null ? $this->roles()->first()->description : "";
    }

    public function getFullNameAttribute($value)
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getProfileImageAttribute($value)
    {
        return asset("storage")."/$value";
    }

}
