<?php 

namespace App\Http\Controllers\Stripe;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use \Carbon\Carbon, Hash, Validator, Log, Throwable;
use App\Helpers\UserHelper;


class UserManagementController extends Controller 
{
    public function customerSubscriptionUpdated(Request $request) {
        try {
            sleep(3);

        } catch(Throwable $e) {

        }
    }

    public function customerInvoicePaymentFailed(Request $request) {
        try {
            sleep(3);

        } catch(Throwable $e) {
            
        }
    }
}
?>