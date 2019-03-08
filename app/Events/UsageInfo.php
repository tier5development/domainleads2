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
use App\SocketMeta;
class UsageInfo implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $socketMeta;
    public function __construct() {
        $this->socketMeta = SocketMeta::first();
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
            'leadsUnlocked'     =>  $this->socketMeta->leads_unlocked,
            'totalDomains'      =>  $this->socketMeta->total_domains,
            'totalUsers'        =>  $this->socketMeta->total_users,
            'leadsAddedLastDay' =>  $this->socketMeta->leads_added_last_day
        ];
    }
}
