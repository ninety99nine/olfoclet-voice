<?php

namespace App\Events;

use App\Models\OmniChannelMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewOmniChannelMessage implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     *
     * @param OmniChannelMessage $message
     */
    public function __construct(OmniChannelMessage $message)
    {
        $this->message = $message;
        Log::info('NewOmniChannelMessage: Event constructed', [
            'messageId' => $message->id,
            'content' => $message->content,
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $channel = new Channel("whatsapp-messages.{$this->message->organization_id}");
        Log::info('NewOmniChannelMessage: Broadcasting on channel', [
            'channel' => "whatsapp-messages.{$this->message->organization_id}",
        ]);
        return $channel;
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'new-message';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $data = [
            'id' => $this->message->id,
            'channel' => $this->message->channel,
            'direction' => $this->message->direction,
            'status' => $this->message->status,
            'from' => $this->message->from,
            'to' => $this->message->to,
            'content' => $this->message->content,
            'message_type' => $this->message->message_type,
            'external_message_id' => $this->message->external_message_id,
            'created_at' => $this->message->created_at->toISOString(),
            'updated_at' => $this->message->updated_at->toISOString(),
        ];
        Log::info('NewOmniChannelMessage: Broadcasting data', [
            'data' => $data,
        ]);
        return $data;
    }

    /**
     * Handle a failure to broadcast.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('NewOmniChannelMessage: Failed to broadcast event', [
            'messageId' => $this->message->id,
            'error' => $exception->getMessage(),
            'stack' => $exception->getTraceAsString(),
        ]);
    }
}
