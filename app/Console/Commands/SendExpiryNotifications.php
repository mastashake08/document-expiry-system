<?php

namespace App\Console\Commands;

use App\Models\Upload;
use App\Notifications\DocumentExpiringNotification;
use Illuminate\Console\Command;

class SendExpiryNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:send-expiry-notifications {--days=7 : Number of days before expiration to send notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for documents expiring within specified days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        
        $this->info("Checking for documents expiring within {$days} days...");
        
        // Get uploads expiring within the specified number of days
        $uploads = Upload::with(['document.client'])
            ->whereBetween('exp_date', [now(), now()->addDays($days)])
            ->whereHas('document.client', function ($query) {
                // Only include clients with email or phone number
                $query->whereNotNull('email')
                    ->orWhereNotNull('phone_number');
            })
            ->get();
        
        if ($uploads->isEmpty()) {
            $this->info('No documents expiring within the specified period.');
            return self::SUCCESS;
        }
        
        $this->info("Found {$uploads->count()} document(s) expiring soon.");
        
        $notificationsSent = 0;
        
        foreach ($uploads as $upload) {
            $client = $upload->document->client;
            
            if (!$client) {
                $this->warn("Document {$upload->document->name} has no associated client. Skipping.");
                continue;
            }
            
            if (!$client->email && !$client->phone_number) {
                $this->warn("Client {$client->name} has no email or phone number. Skipping.");
                continue;
            }
            
            try {
                $client->notify(new DocumentExpiringNotification($upload));
                $notificationsSent++;
                $this->line("✓ Notification sent to {$client->name} for document: {$upload->document->name}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to send notification to {$client->name}: " . $e->getMessage());
            }
        }
        
        $this->newLine();
        $this->info("Successfully sent {$notificationsSent} notification(s).");
        
        return self::SUCCESS;
    }
}
