<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Pelanggaran implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $peringatan;

    public function __construct($user, $peringatan)
    {
        $this->user = $user;
        $this->peringatan = $peringatan;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('admin.pelanggaran');
    }

    public function broadcastAs(): string
    {
        return 'pelanggaran';
    }
}
