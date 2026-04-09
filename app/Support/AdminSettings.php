<?php

namespace App\Support;

class AdminSettings
{
    private static ?array $cached = null;

    public static function read(): array
    {
        if (self::$cached !== null) {
            return self::$cached;
        }

        $file = storage_path('app/admin_settings.json');
        $defaults = [
            'maintenance_mode' => false,
            'message' => null,
        ];

        if (!file_exists($file)) {
            return self::$cached = $defaults;
        }

        try {
            $json = json_decode((string) file_get_contents($file), true);
            if (!is_array($json)) {
                return self::$cached = $defaults;
            }
            return self::$cached = array_merge($defaults, $json);
        } catch (\Throwable $_e) {
            return self::$cached = $defaults;
        }
    }

    public static function maintenanceEnabled(): bool
    {
        $settings = self::read();
        return !empty($settings['maintenance_mode']);
    }

    public static function maintenanceMessage(): ?string
    {
        $settings = self::read();
        $msg = $settings['message'] ?? null;
        return is_string($msg) && trim($msg) !== '' ? trim($msg) : null;
    }
}
