<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

// use App\LeadUser;
// use App\EachDomain;
// use App\User;
// use App\Lead;
use App\SocketMeta;
use \Carbon\Carbon;
class UsageInfo implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // public $leadsUnlocked;
    // public $totalDomains;
    // public $totalUsers;
    // public $leadsAddedLastDay;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct() {
        // $this->leadsUnlocked        =   LeadUser::count();
        // $this->totalDomains         =   EachDomain::count();
        // $this->totalUsers           =   User::count();
        // $this->leadsAddedLastDay    =   Lead::where('created_at', Carbon::yesterday())->count();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn() {
        // return new PrivateChannel('channel-name');
        return new Channel('usage-info');
        // return ['usage-info'];
    }

    public function broadcastWith() {
        return [
            'leadsUnlocked'     =>  $this->leadsUnlocked,
            'totalDomains'      =>  $this->totalDomains,
            'totalUsers'        =>  $this->totalUsers,
            'leadsAddedLastDay' =>  $this->leadsAddedLastDay
        ];
    }
}
