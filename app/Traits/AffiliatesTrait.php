<?php 
namespace App\Traits;
use App\User;
use App\Helpers\StripeHelper;
use Illuminate\Http\Request;
use App\StripeDetails;
use Log, Hash, Auth, Session, Exception, Throwable, DB, View, Mail;
use App\Helpers\UserHelper;
use \Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

trait AffiliatesTrait {

    /**
     * This function communicates with affiliate portal and maintains to keep sync of the sales status of a user with that platform.
     * Dependency : called every time some one 
     * 1> Makes a successful registration [type:create],
     * 2> Upgrades or downgrades their plan [type:update],
     * 3> If subscription payment did not go through or subscription canceled [type:inactive],
     * 4> If subscription resumed after an inactive state [type:active],
     * 
     * @param
     * type : [create, update, active, inactive]
     * user : user model instance
     * trial : trial period info
     * 
     * @return
     * void
     */
    public function registerSale($type, $user, $trial = 0) {
        try {
            
            Log::info('**** IN registerSale **** '.$type);
            $plan = $user->user_type;
            $amount = config('settings.PLAN.PUBLISHABLE.'.$plan)[1];
            $url = '/hooks/sales';
            $client = new Client();
            switch($type) {
                case 'created' : 
                Log::info('url : '.config('settings.AFFILIATE-HOOK'));
                    $result = $client->post(config('settings.AFFILIATE-HOOK'), [
                        'form_params' => [
                                "product_name"      => config('settings.PRODUCT'),
                                "ammount"  	 	    => $amount,
                                "payment_type" 		=> 1,
                                "trial_period" 		=> $trial,
                                "date_registered"   => Carbon::now()->format('Y-m-d'),
                                "affiliateId" 	    => $user->affiliate_id,
                                "email"  		    => $user->email
                        ]
                    ])->getBody()->getContents();
                    $res = json_decode(str_replace("\n", "", $result));
                    if($res->httpCode == 200) {
                        $user->updateSale($res);
                        Log::info('User updated successfully');
                    } else {
                        Log::info('User not updated');
                    }
                    break;
                case 'updated' : 
                    $result = $client->post(config('settings.AFFILIATE-HOOK'), [
                        'form_params' => [
                                "saleId"            => $user->sale_id,
                                "product_name" 		=> config('settings.PRODUCT'),
                                "ammount"  	 	    => $amount,
                                "payment_type" 		=> 1,
                        ]
                    ])->getBody()->getContents();
                    break;
                case 'active' : 
                    $result = $client->post(config('settings.AFFILIATE-HOOK'), [
                        'form_params' => [
                                "saleId"            => $user->sale_id,
                                "is_active" 		=> true
                        ]
                    ])->getBody()->getContents();
                    break;
                case 'inactive' : 
                    $result = $client->post(config('settings.AFFILIATE-HOOK'), [
                        'form_params' => [
                                "saleId"            => $user->sale_id,
                                "is_active" 		=> false
                        ]
                    ])->getBody()->getContents();
                    break;
                default : Log::info('Incorrect type in registerSale type : '.$type);
                    break;
            }
        } catch(Throwable $e) {
            Log::info('Error in register sale : '.$e->getMessage());
            throw $e;
        }
    }
}
