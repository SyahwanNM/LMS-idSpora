<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('Testing email configuration...');
        
        // Display current mail configuration
        $this->info('Mail Configuration:');
        $this->line('MAIL_MAILER: ' . config('mail.default'));
        $this->line('MAIL_HOST: ' . config('mail.mailers.smtp.host'));
        $this->line('MAIL_PORT: ' . config('mail.mailers.smtp.port'));
        $this->line('MAIL_USERNAME: ' . config('mail.mailers.smtp.username'));
        $this->line('MAIL_PASSWORD: ' . (config('mail.mailers.smtp.password') ? 'SET' : 'NOT SET'));
        $this->line('MAIL_ENCRYPTION: ' . config('mail.mailers.smtp.encryption'));
        $this->line('MAIL_FROM_ADDRESS: ' . config('mail.from.address'));
        $this->line('MAIL_FROM_NAME: ' . config('mail.from.name'));
        
        try {
            $this->info("\nSending test email to: {$email}");
            
            Mail::to($email)->send(new PasswordResetMail('123456', 'Test User'));
            
            $this->info('✅ Email sent successfully!');
            
        } catch (\Exception $e) {
            $this->error('❌ Email failed: ' . $e->getMessage());
            $this->error('Error details: ' . $e->getTraceAsString());
        }
    }
}
