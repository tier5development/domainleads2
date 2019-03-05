<?php 
namespace App\Traits;
use App\User;
use App\Helpers\StripeHelper;
use Illuminate\Http\Request;
use App\StripeDetails;
use Log, Hash, Auth, Session, Exception, Throwable, DB, View, Mail;
use App\Helpers\UserHelper;
use \Carbon\Carbon;

trait EmailTrait {


    /**
        * Email function example and available methods

        * Mail::send('emails.welcome', $data, function($message)
        * {
        *     $message->to('foo@example.com', 'name1')
        *         ->replyTo('reply@example.com', 'name2')
        *         ->subject('Welcome!');
        * });
        * There are various methods you can call on it:
        * 
        * ->from($address, $name = null)
        * ->sender($address, $name = null)
        * ->returnPath($address)
        * ->to($address, $name = null)
        * ->cc($address, $name = null)
        * ->bcc($address, $name = null)
        * ->replyTo($address, $name = null)
        * ->subject($subject)
        * ->priority($level)
        * ->attach($file, array $options = array())
        * ->attachData($data, $name, array $options = array())
        * ->embed($file)
        * ->embedData($data, $name, $contentType = null)
        * Plus, there is a magic __call method, so you can run any method that you would normally run on the underlying SwiftMailer class.
    */

    public function sendEmailConfirmation($user) {
        try {
            if(!$user) {
                return ['status' => false, 'message' => 'User cannot be null'];
            } else if($user->email_verified == 1) {
                return ['status' => false, 'message' => 'Email is already verified'];
            }
    
            $adminEmail =   config('settings.ADMIN-EMAIL');
            $userName   =   $user->name;
            $email      =   $user->email;
            $title      =   "Domainleads email verification.";
            $subject    =   "Email verification.";
            $content    =   "Please verify your email here.";
            Mail::send('emails.emailverification', [
                'title'     =>  $title, 
                'content'   =>  $content,
                'user'      =>  $user
            ], function ($message) use ($adminEmail, $email, $userName, $subject) {
                $message->from($adminEmail);
                $message->to($email, $userName);
                $message->subject($subject);
            });

            if(count(Mail::failures()) > 0) {
                Log::info('Sent email for email verification failed');
                return ['status' => false, 'Mail was not sucessfully sent. Please try again'];
            }
            return ['status' => true, 'Mail was sucessfully sent.'];
        } catch(Throwable $e) {
            Log::info('Sent email for email verification failed as : '.$e->getMessage());
            throw $e;
        }
    }

    public function sendFirstEmailOnSuccessfulRegistration($user) {
        try {
            $adminEmail =   config('settings.ADMIN-EMAIL');
            $userName   =   $user->name;
            $email      =   $user->email;
            $title      =   "Thanks for signing up with DomainLeads";
            $subject    =   "DomainLeads Signup";
            $content    =   "Thanks for signing up with DomainLeads. Now you can use this software to get fresh leads of domain registrants every day!";
            Mail::send('emails.thankyou', [
                'title'     =>  $title, 
                'content'   =>  $content,
                'user_name' =>  $userName
            ], function ($message) use ($adminEmail, $email, $userName, $subject)
            {
                $message->from($adminEmail);
                $message->to($email, $userName);
                $message->subject($subject);
            });

            if(count(Mail::failures()) > 0){
                Log::info(' sent email for confirmation failed ');
                return ['status' => false, 'Mail was not sucessfully sent. Please try again'];
            }
            return ['status' => true, 'Mail was sucessfully sent.'];
        } catch(Throwable $e) {
            Log::info(' sent email for confirmation failed : '.$e->getMessage());
            throw $e;
        }
    }

    public function sendAcknowledgeMailToAdmin($user) {
        try {
            $adminEmail =   config('settings.ADMIN-EMAIL');
            $userName   =   $user->name;
            $email      =   $user->email;
            $title      =   "New registration in Domainleads";
            $subject    =   "DomainLeads New Signup";
            $content    =   "A new user : ".$user->name." and email : ".$user->email." joined in domainleads platform.";
            Mail::send('emails.accback', [
                'title'     =>  $title, 
                'content'   =>  $content,
                'user_name' =>  $userName,
                'email'     =>  $email
            ], function ($message)use ($adminEmail, $email, $userName, $subject)
            {
                $message->from($email, $userName);
                $message->to($adminEmail);
                $message->subject($subject);
            });
            if(count(Mail::failures()) > 0) {
                Log::info(' sent email for admin acknowledgement failed ');
                return ['status' => false, 'Mail was not sucessfully sent. Please try again'];
            }
            return ['status' => true, 'Mail was sucessfully sent.'];
        } catch(Throwable $e) {
            Log::info(' sent email for admin acknowledgement failed : '.$e->getMessage());
            throw $e;
        }
    }
}
?>