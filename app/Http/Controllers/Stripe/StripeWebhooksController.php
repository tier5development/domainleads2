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
            Log::info('subscription request : ', $request->all());
        } catch(Throwable $e) {
            Log::info('subscription request ERROR :::: '.$e->getMessage());
        }
    }

    public function customerInvoicePaymentFailed(Request $request) {
        try {
            sleep(3);
            Log::info('invoice request : ', $request->all());
        } catch(Throwable $e) {
            Log::info('invoice request ERROR :::: '.$e->getMessage());
        }
    }
}
?>