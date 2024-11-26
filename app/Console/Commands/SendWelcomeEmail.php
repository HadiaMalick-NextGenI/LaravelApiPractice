<?php

namespace App\Console\Commands;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-welcome-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send welcome emails to users who registered in the current month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('welcome_email_sent',false)
            ->get();

        if ($users->isEmpty()) {
            $this->info('No new users found to send welcome emails this month.');
            return;
        }

        foreach ($users as $user) {
            try{
                Mail::to($user->email)->send(new WelcomeEmail($user));
                //$user->update(['welcome_email_sent' => true]);
                $user->welcome_email_sent = true;
                if ($user->save()) {
                    $this->info("Email sent and status updated for {$user->email}");
                } else {
                    $this->error("Failed to update status for {$user->email}");
                }

            }catch(Exception $e){
                $this->error( `Failed to send email to: $user->email`);
                $this->error("Error: {$e->getMessage()}");
                Log::error("Email sending failed for user: {$user->email}. Error: " . $e->getMessage());
            }
        }

        $this->info('Welcome emails sent successfully to users who registered this month!');
    }
}
