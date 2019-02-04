<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripeDetails extends Model
{
    use SoftDeletes;
    protected $table = 'stripe_details';
}
