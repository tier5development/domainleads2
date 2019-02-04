<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table='users';

    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function leads()
    {
        return $this->belongsToMany('App\Lead','leadusers','user_id','id');
    }

    public function allowedToCancelMembership() {
        return strlen(trim($this->affiliate_id)) > 0 && $this->user_type > $this->base_type && $this->is_hooked == '1' ? true : false;
    }
    
}
