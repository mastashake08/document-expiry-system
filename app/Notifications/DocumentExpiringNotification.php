<?php

namespace App\Notifications;

use App\Models\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class DocumentExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Upload $upload
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail'];
        
        // Only send SMS if client has a phone number and Twilio is configured
        if ($notifiable->phone_number && config('services.twilio.sid')) {
            $channels[] = TwilioChannel::class;
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $daysUntilExpiry = now()->diffInDays($this->upload->exp_date, false);
        $documentName = $this->upload->document->name;
        
        return (new MailMessage)
            ->subject('Document Expiring Soon: ' . $documentName)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is a reminder that your document "' . $documentName . '" is expiring soon.')
            ->line('Expiration Date: ' . $this->upload->exp_date->format('F j, Y'))
            ->line('Days remaining: ' . max(0, $daysUntilExpiry))
            ->action('View Document', url('/documents/' . $this->upload->document->id))
            ->line('Please upload a new version before the expiration date to avoid any disruptions.');
    }

    /**
     * Get the Twilio / SMS representation of the notification.
     */
    public function toTwilio(object $notifiable): TwilioSmsMessage
    {
        $daysUntilExpiry = now()->diffInDays($this->upload->exp_date, false);
        $documentName = $this->upload->document->name;
        
        $message = "Document Alert: '{$documentName}' expires on " . 
                   $this->upload->exp_date->format('M j, Y') . 
                   ' (' . max(0, $daysUntilExpiry) . ' days). Please renew soon.';
        
        return (new TwilioSmsMessage())
            ->content($message);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'upload_id' => $this->upload->id,
            'document_id' => $this->upload->document->id,
            'document_name' => $this->upload->document->name,
            'exp_date' => $this->upload->exp_date,
        ];
    }
}
