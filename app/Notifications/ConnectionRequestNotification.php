<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class ConnectionRequestNotification extends Notification
{
    use Queueable;

    protected $fromUser;
    protected $connectionId;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $fromUser, $connectionId)
    {
        $this->fromUser = $fromUser;
        $this->connectionId = $connectionId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'connection_request',
            'from_user_id' => $this->fromUser->id_usuarios,
            'from_user_name' => $this->fromUser->nome,
            'from_user_avatar' => $this->fromUser->url_foto,
            'connection_id' => $this->connectionId,
            'message' => "{$this->fromUser->nome} quer se conectar com você"
        ];
    }
}
