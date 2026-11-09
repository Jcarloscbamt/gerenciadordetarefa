<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** E-mail enviado ao usuário quando seu autocadastro é aprovado. */
class CadastroAprovado extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Seu cadastro foi aprovado! ✅')
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Seu cadastro no **Gerenciador de Tarefas** foi aprovado por um administrador.')
            ->line('Você já pode acessar o sistema normalmente com seu e-mail e senha.')
            ->action('Acessar o sistema', route('login'))
            ->line('Se você não solicitou este cadastro, pode ignorar este e-mail.')
            ->salutation('— Equipe Gerenciador de Tarefas');
    }
}
