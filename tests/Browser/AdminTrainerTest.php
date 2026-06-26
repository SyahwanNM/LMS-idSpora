<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminTrainerTest extends DuskTestCase
{
    /**
     * Uji akses dashboard admin trainer dan cek metrik.
     */
    public function testAdminDashboardAndMetrics()
    {
        $this->browse(function (Browser $browser) {
            // Cari salah satu akun admin di database untuk login otomatis
            $admin = User::where('role', 'admin')->first();

            $browser->loginAs($admin)
                    ->visit('/admin/trainer')
                    ->waitForText('Admin Trainer!', 10) // Tunggu teks selamat datang muncul
                    ->assertSee('TARGET BULANAN') // Cek target bulanan
                    ->assertPresent('.table-premium') // Pastikan tabel trainer dirender
                    ->screenshot('dashboard_admin_trainer_verified'); // Simpan screenshot hasil uji
        });
    }

    /**
     * Uji alur CRUD lengkap (Create, Read, Update) akun Trainer oleh Admin.
     */
    public function testAdminCanManageTrainerLifecycle()
    {
        // 1. Bersihkan database terlebih dahulu jika data sisa tes sebelumnya ada
        User::where('email', 'trainerdusk@idspora.com')->delete();

        $this->browse(function (Browser $browser) {
            $admin = User::where('role', 'admin')->first();

            // Login & Menuju Halaman Buat Trainer Baru
            $browser->loginAs($admin)
                    ->visit('/admin/trainer')
                    ->clickLink('Tambah Trainer Baru')
                    ->assertPathIs('/admin/trainer/create')
                    ->type('name', 'Trainer Dusk Test')
                    ->type('email', 'trainerdusk@idspora.com')
                    ->type('phone', '08123456789')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->type('profession', 'Automation Tester')
                    ->type('institution', 'Dusk Labs')
                    ->type('bio', 'Ini adalah bio trainer test yang dibuat secara otomatis oleh Dusk.')
                    ->press('Simpan & Daftarkan') // Tekan tombol submit
                    
                    // Verifikasi Berhasil Dibuat & Redirect
                    ->assertPathIs('/admin/trainer')
                    ->assertSee('Trainer berhasil dibuat!');

            // Ambil objek trainer yang baru dibuat dari DB
            $trainer = User::where('email', 'trainerdusk@idspora.com')->first();
            $this->assertNotNull($trainer);

            // Buka halaman edit trainer & perbarui data nomor whatsapp
            $browser->visit("/admin/trainer/{$trainer->id}/edit")
                    ->type('phone', '0899999999')
                    ->press('Simpan Perubahan') // Simpan data edit
                    
                    // Verifikasi Berhasil Diperbarui & dialihkan ke halaman detail profil
                    ->assertPathIs("/admin/trainer/{$trainer->id}")
                    ->assertSee('Data trainer berhasil diperbarui!')
                    ->assertSee('0899999999');
        });

        // 2. Bersihkan database setelah pengujian selesai
        User::where('email', 'trainerdusk@idspora.com')->delete();
    }
}
