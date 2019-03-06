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
        if(strlen(trim($this->affiliate_id)) == 0) {
            return true;
        }
        // else we only care if this user is attached to stripe
        return strlen(trim($this->stripe_customer_id)) > 0 ? true : false;
        // return strlen(trim($this->affiliate_id)) > 0 && $this->user_type > $this->base_type && $this->is_hooked == '1' ? true : false;
    }

    public function isDowngradable() {
        if(strlen(trim($this->affiliate_id)) == 0) {
            return true;
        }
        // else we only care if this user is attached to stripe
        return strlen(trim($this->stripe_customer_id)) > 0 ? true : false;
        // return strlen(trim($this->affiliate_id)) > 0 && $this->user_type > $this->base_type && $this->is_hooked == '1' ? true : false;
    }

    /**
     * This update is called when a sale is registered
     */
    public function updateSale($res) {
        $user = $this;
        $user->sale_id = $res->payload->saleId;
        $user->save();
    }
}
