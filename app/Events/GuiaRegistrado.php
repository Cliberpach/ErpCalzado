<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GuiaRegistrado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $guia;
    public $serie; 
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($guia , $serie)
    {
        $this->guia = $guia;
        $this->serie = $serie;
    }
}
