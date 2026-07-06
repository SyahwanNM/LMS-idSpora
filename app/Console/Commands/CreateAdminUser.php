<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {name?} {email?} {password?}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Create a new admin user account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get input from arguments or ask for input
        $name = $this->argument('name') ?? $this->ask('Masukkan nama admin');
        $email = $this->argument('email') ?? $this->ask('Masukkan email admin');
        $password = $this->argument('password') ?? $this->secret('Masukkan password admin');

        // Validate
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Format email tidak valid!');
            return 1;
        }

        if (strlen($password) < 6) {
            $this->error('Password minimal 6 karakter!');
            return 1;
        }

        // Check if email already exists
        if (User::where('email', $email)->exists()) {
            $this->error("Email '{$email}' sudah terdaftar!");
            return 1;
        }

        // Create user
        $admin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'user_status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->info("✓ Akun admin berhasil dibuat!");
        $this->table(
            ['Field', 'Value'],
            [
                ['Nama', $admin->name],
                ['Email', $admin->email],
                ['Role', $admin->role],
                ['ID', $admin->id],
            ]
        );

        return 0;
    }
}
