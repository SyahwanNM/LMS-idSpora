<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Event;
use App\Models\TrainerCertificate;
use App\Models\TrainerCertificateAsset;
use Carbon\CarbonInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TrainerCertificateService
{
    public function defaultActivityCode(Event|Course $certifiable, string $context): string
    {
        $map = [
            'webinar' => 'WBN',
            'seminar' => 'SMN',
            'workshop' => 'WRT',
            'training' => 'WRT',
            'video production' => 'VDP',
            'video' => 'VDP',
            'e-learning' => 'ELR',
            'elearning' => 'ELR',
        ];

        if ($context === 'event') {
            return $map[strtolower((string) ($certifiable->jenis ?? ''))] ?? 'WBN';
        }

        return $map[
            strtolower((string) (
                $certifiable->certificate_category
                ?? $certifiable->learning_type
                ?? 'e-learning'
            ))
        ] ?? 'ELR';
    }

    public function nextSequence(
        string $activityCode,
        string $typeCode,
        CarbonInterface $issuedAt
    ): string {
        $maxSequence = TrainerCertificate::query()
            ->where('activity_code', strtoupper($activityCode))
            ->where('type_code', strtoupper($typeCode))
            ->whereYear('issued_at', $issuedAt->year)
            ->whereMonth('issued_at', $issuedAt->month)
            ->lockForUpdate()
            ->max(DB::raw("CAST(sequence AS UNSIGNED)")) ?? 0;

        return str_pad(
            (string) ((int) $maxSequence + 1),
            3,
            '0',
            STR_PAD_LEFT
        );
    }

    public function buildCertificateNumber(
        string $activityCode,
        string $typeCode,
        string $sequence,
        CarbonInterface $issuedAt
    ): string {
        $romanMonths = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return 'IDSP/'
            . strtoupper($activityCode) . '/'
            . strtoupper($typeCode) . '/'
            . $sequence . '/'
            . $romanMonths[(int) $issuedAt->format('n')] . '/'
            . $issuedAt->format('Y');
    }

    public function validateAssetsReady(Event|Course $certifiable): void
    {
        $logos = TrainerCertificateAsset::query()
            ->where('certifiable_type', $certifiable::class)
            ->where('certifiable_id', (int) $certifiable->id)
            ->where('type', TrainerCertificateAsset::TYPE_LOGO)
            ->count();

        $signatures = TrainerCertificateAsset::query()
            ->where('certifiable_type', $certifiable::class)
            ->where('certifiable_id', (int) $certifiable->id)
            ->where('type', TrainerCertificateAsset::TYPE_SIGNATURE)
            ->count();

        if ($logos < 1 || $logos > 3) {
            abort(422, 'Logo sertifikat wajib minimal 1 dan maksimal 3.');
        }

        if ($signatures < 1 || $signatures > 3) {
            abort(422, 'Tanda tangan sertifikat wajib minimal 1 dan maksimal 3.');
        }
    }

    public function assetsForPdf(Event|Course $certifiable, string $type): array
    {
        return TrainerCertificateAsset::query()
            ->where('certifiable_type', $certifiable::class)
            ->where('certifiable_id', (int) $certifiable->id)
            ->where('type', $type)
            ->orderBy('order_no')
            ->get()
            ->map(function (TrainerCertificateAsset $asset) {
                $path = str_replace('storage/', '', (string) $asset->image_path);

                if ($path === '' || !Storage::disk('public')->exists($path)) {
                    return null;
                }

                $absolutePath = Storage::disk('public')->path($path);
                $mime = is_file($absolutePath)
                    ? (mime_content_type($absolutePath) ?: 'application/octet-stream')
                    : 'application/octet-stream';

                return [
                    'image' => 'data:' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($path)),
                    'name' => $asset->name,
                    'position' => $asset->position,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    public function certificateTypeLabel(string $typeCode): string
    {
        return [
            'TRN' => 'Narasumber',
        ][strtoupper(trim($typeCode))] ?? strtoupper(trim($typeCode));
    }

    public function saveAssets(Event|Course $certifiable, array $assets): void
    {
        DB::transaction(function () use ($certifiable, $assets) {

            // Hapus asset lama
            TrainerCertificateAsset::query()
                ->where('certifiable_type', $certifiable::class)
                ->where('certifiable_id', (int) $certifiable->id)
                ->delete();

            foreach (['logo', 'signature'] as $type) {

                $items = array_values($assets[$type] ?? []);

                // Maksimal 3
                $items = array_slice($items, 0, 3);

                foreach ($items as $index => $item) {

                    $file = $item['file'] ?? null;
                    $existingPath = (string) ($item['existing_path'] ?? '');

                    if ($file instanceof UploadedFile) {

                        $path = $file->store(
                            'trainer_certificate_assets/' .
                            class_basename($certifiable) .
                            '/' .
                            $certifiable->id,
                            'public'
                        );

                    } else {

                        $path = $existingPath;
                    }

                    if ($path === '') {
                        continue;
                    }

                    TrainerCertificateAsset::query()->create([
                        'certifiable_type' => $certifiable::class,
                        'certifiable_id' => (int) $certifiable->id,
                        'type' => $type,
                        'name' => $type === 'signature'
                            ? ($item['name'] ?? null)
                            : null,
                        'position' => $type === 'signature'
                            ? ($item['position'] ?? null)
                            : null,
                        'image_path' => $path,
                        'order_no' => $index + 1,
                    ]);
                }
            }
        });
    }
}