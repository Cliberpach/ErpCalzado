<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FacturacionEmpresa
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $empresa;
    public $numeracion_empresa;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($empresa , $numeracion_empresa)
    {
        $this->empresa = $empresa;
        $this->numeracion_empresa = $numeracion_empresa;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
