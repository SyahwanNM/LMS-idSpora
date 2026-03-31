<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Course;
use App\Models\Event;
use App\Models\TrainerCertificate;
use App\Models\TrainerNotification;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Illuminate\Support\Str;

class TrainerManagementController extends Controller
{
    // Menampilkan daftar trainer
    public function index(Request $request)
    {
        $totalTrainers = User::where('role', 'trainer')->count();
        $activeTrainers = User::where('role', 'trainer')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $teachingTrainers = User::where('role', 'trainer')
            ->where(function ($query) {
                $query->whereHas('coursesAsTrainer')
                    ->orWhereHas('eventsAsTrainer');
            })
            ->count();

        $query = User::where('role', 'trainer')
            ->withCount(['coursesAsTrainer', 'eventsAsTrainer'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Sorting Logic
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
            }
        }

        $trainers = $query->paginate(10);

        return view('admin.trainer.index', compact('trainers', 'totalTrainers', 'activeTrainers', 'teachingTrainers'));
    }

    public function create()
    {
        return view('admin.trainer.create');
    }

    // [UPDATED] SIMPAN TRAINER BARU
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:30'],
            'profession' => ['nullable', 'string', 'max:100'], // Field Baru
            'institution' => ['nullable', 'string', 'max:255'], // Field Baru
            'website' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // Validasi Foto
        ]);

        $data['role'] = 'trainer';
        $data['password'] = Hash::make($data['password']);

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        User::create($data);

        return redirect()->route('admin.trainer.index')->with('success', 'Trainer berhasil dibuat!');
    }

    public function show(User $trainer)
    {
        if ($trainer->role !== 'trainer')
            abort(404);

        $trainer->loadCount(['coursesAsTrainer', 'eventsAsTrainer']);

        $trainerCourses = Course::query()
            ->where('trainer_id', $trainer->id)
            ->orderByDesc('approved_at')
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'status', 'approved_at', 'created_at']);

        $trainerEvents = Event::query()
            ->where('trainer_id', $trainer->id)
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'jenis', 'event_date', 'created_at', 'certificate_template']);

        $trainerCertificates = TrainerCertificate::query()
            ->with(['issuer:id,name', 'certifiable'])
            ->where('trainer_id', $trainer->id)
            ->latest('issued_at')
            ->latest('created_at')
            ->get();

        return view('admin.trainer.show', compact('trainer', 'trainerCourses', 'trainerEvents', 'trainerCertificates'));
    }

    public function issueCertificate(Request $request, User $trainer)
    {
        if ($trainer->role !== 'trainer') {
            abort(404);
        }

        $data = $request->validate([
            'context' => ['required', 'in:event,course'],
            'context_id' => ['required', 'integer'],
            'activity_code' => ['required', 'string', 'size:3'],
            'type_code' => ['required', 'string', 'min:2', 'max:3'],
            'sequence' => ['required', 'string', 'max:10'],
            'issued_at' => ['nullable', 'date'],
        ]);

        $issuedAt = $request->filled('issued_at')
            ? \Carbon\Carbon::parse($data['issued_at'])
            : now();

        $certifiableType = $data['context'] === 'event' ? Event::class : Course::class;
        $certifiable = ($data['context'] === 'event')
            ? Event::where('id', $data['context_id'])->where('trainer_id', $trainer->id)->firstOrFail()
            : Course::where('id', $data['context_id'])->where('trainer_id', $trainer->id)->firstOrFail();

        $certificateNumber = $this->buildIdsporaCertificateNumber(
            $data['activity_code'],
            $data['type_code'],
            $data['sequence'],
            $issuedAt
        );

        $exists = TrainerCertificate::where('certificate_number', $certificateNumber)->exists();
        if ($exists) {
            // Informational only: duplicate certificate numbers are expected in some trainer flows
            return back()->with('info', "Nomor sertifikat sudah dipakai: {$certificateNumber}. Sesuaikan nomor urut jika perlu.");
        }

        $trainerCertificate = TrainerCertificate::create([
            'trainer_id' => $trainer->id,
            'certifiable_type' => $certifiableType,
            'certifiable_id' => $certifiable->id,
            'activity_code' => strtoupper($data['activity_code']),
            'type_code' => strtoupper($data['type_code']),
            'sequence' => $data['sequence'],
            'certificate_number' => $certificateNumber,
            'issued_at' => $issuedAt,
            'issued_by' => Auth::id(),
            'status' => 'sent',
        ]);

        // Generate & store PDF so trainer download can fetch from file_path
        $roleLabelMap = [
            'SRT' => 'Peserta',
            'MC' => 'MC',
            'TRN' => 'Narasumber',
            'PNT' => 'Panitia',
            'CLB' => 'Kolaborator',
            'MOD' => 'Moderator',
            'GRD' => 'Kelulusan',
            'SPV' => 'Supervisor/penilai',
        ];
        $roleLabel = $roleLabelMap[strtoupper((string) $data['type_code'])] ?? strtoupper((string) $data['type_code']);

        $logosBase64 = [];
        $signaturesBase64 = [];
        if ($data['context'] === 'event') {
            $event = $certifiable;
            foreach (is_array($event->certificate_logo) ? $event->certificate_logo : [] as $l) {
                $path = str_replace('storage/', '', (string) $l);
                if ($path !== '' && Storage::disk('public')->exists($path)) {
                    $absolutePath = Storage::disk('public')->path($path);
                    $mime = (is_string($absolutePath) && is_file($absolutePath)) ? (mime_content_type($absolutePath) ?: 'application/octet-stream') : 'application/octet-stream';
                    $logosBase64[] = 'data:' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($path));
                }
            }
            foreach (is_array($event->certificate_signature) ? $event->certificate_signature : [] as $s) {
                $path = str_replace('storage/', '', (string) $s);
                if ($path !== '' && Storage::disk('public')->exists($path)) {
                    $absolutePath = Storage::disk('public')->path($path);
                    $mime = (is_string($absolutePath) && is_file($absolutePath)) ? (mime_content_type($absolutePath) ?: 'application/octet-stream') : 'application/octet-stream';
                    $signaturesBase64[] = 'data:' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($path));
                }
            }
        }

        $pdfData = [
            'context' => $data['context'],
            'event' => $data['context'] === 'event' ? $certifiable : null,
            'course' => $data['context'] === 'course' ? $certifiable : null,
            'user' => $trainer,
            'issuedAt' => $issuedAt,
            'certificateNumber' => $certificateNumber,
            'logosBase64' => $logosBase64,
            'signaturesBase64' => $signaturesBase64,
            'roleLabel' => $roleLabel,
        ];

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->setIsRemoteEnabled(true);
        $options->setIsHtml5ParserEnabled(true);
        $dompdf->setOptions($options);

        $html = view('trainer.certificates.certificate-pdf', $pdfData)->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $relativeDir = 'trainer_certificates/' . $trainer->id . '/' . $data['context'] . '/' . $certifiable->id;
        $filename = Str::slug($certificateNumber, '_') . '.pdf';
        $relativePath = $relativeDir . '/' . $filename;
        $absolutePath = storage_path('app/' . $relativePath);

        if (!is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }
        file_put_contents($absolutePath, $dompdf->output());

        $trainerCertificate->update(['file_path' => $relativePath]);

        $contextLabel = $data['context'] === 'event' ? 'event' : 'course';
        $contextTitle = $data['context'] === 'event'
            ? (string) ($certifiable->title ?? '')
            : (string) ($certifiable->name ?? '');
        $trainerUrl = route('trainer.certificates.index') . '?context=' . $data['context'] . '&id=' . (int) $certifiable->id;

        TrainerNotification::create([
            'trainer_id' => $trainer->id,
            'type' => 'certificate_issued',
            'title' => 'Sertifikat telah diterbitkan',
            'message' => 'Sertifikat untuk ' . $contextLabel . ($contextTitle !== '' ? ' "' . $contextTitle . '"' : '') . ' sudah tersedia. No: ' . $certificateNumber,
            'data' => [
                'entity_type' => $data['context'],
                'entity_id' => (int) $certifiable->id,
                'certificate_number' => $certificateNumber,
                'url' => $trainerUrl,
            ],
            'expires_at' => now()->addDays(30),
        ]);

        return back()->with('success', "Sertifikat trainer diterbitkan: {$certificateNumber}");
    }

    /**
     * Admin uploads a ready-made certificate PDF for a trainer (manual send).
     */
    public function sendCertificate(Request $request, User $trainer)
    {
        if ($trainer->role !== 'trainer') {
            abort(404);
        }

        $data = $request->validate([
            'recipient' => ['required', 'string', 'max:255'],
            'certificate_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $file = $request->file('certificate_file');
        $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
        $relativeDir = 'trainer_certificates/' . $trainer->id . '/manual';
        $relativePath = $file->storeAs($relativeDir, $filename);

        $certificateNumber = 'MANUAL-' . time() . '-' . Str::upper(Str::random(6));

        $trainerCertificate = TrainerCertificate::create([
            'trainer_id' => $trainer->id,
            'file_path' => $relativePath,
            'status' => 'sent',
            'issued_at' => now(),
            'issued_by' => Auth::id(),
            'certificate_number' => $certificateNumber,
        ]);

        TrainerNotification::create([
            'trainer_id' => $trainer->id,
            'type' => 'certificate_issued',
            'title' => 'Sertifikat telah diterbitkan (manual)',
            'message' => 'Sertifikat manual telah diunggah: ' . $certificateNumber,
            'data' => [
                'certificate_number' => $certificateNumber,
                'url' => route('trainer.certificates.index')
            ],
            'expires_at' => now()->addDays(30),
        ]);

        return back()->with('success', 'Sertifikat manual berhasil diunggah dan disimpan.');
    }

    /**
     * Show admin form to upload/send a certificate for the given trainer.
     */
    public function showSendCertificateForm(User $trainer)
    {
        if ($trainer->role !== 'trainer') {
            abort(404);
        }

        return view('admin.trainer.send_certificate', compact('trainer'));
    }

    /**
     * Display trainer certificates queue for admin.
     */
    public function certificatesQueue(Request $request)
    {
        $query = User::where('role', 'trainer')->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        } else {
            $search = null;
        }

        $trainers = $query->paginate(15);

        // Ensure the view attributes exist (simple defaults)
        $trainers->getCollection()->transform(function ($t) {
            $t->pending_events_certificates = $t->pending_events_certificates ?? 0;
            $t->pending_courses_certificates = $t->pending_courses_certificates ?? 0;
            $t->pending_certificates_count = $t->pending_certificates_count ?? 0;
            return $t;
        });

        return view('admin.trainer.certificates_queue', compact('trainers', 'search'));
    }

    /**
     * Return rendered certificate HTML for preview (AJAX).
     */
    public function previewCertificate(Request $request)
    {
        $data = $request->validate([
            'trainer_id' => ['required', 'integer'],
            'context' => ['required', 'in:event,course'],
            'context_id' => ['required', 'integer'],
            'activity_code' => ['required', 'string', 'size:3'],
            'type_code' => ['required', 'string', 'min:2', 'max:3'],
            'sequence' => ['required', 'string'],
            'issued_at' => ['nullable', 'date'],
        ]);

        $trainer = User::find($data['trainer_id']);
        if (!$trainer) {
            return response('Trainer not found', 404);
        }

        $issuedAt = $request->filled('issued_at') ? \Carbon\Carbon::parse($data['issued_at']) : now();

        $certifiable = null;
        $certifiableType = $data['context'] === 'event' ? Event::class : Course::class;
        if ($data['context'] === 'event') {
            $certifiable = Event::where('id', $data['context_id'])->first();
        } else {
            $certifiable = Course::where('id', $data['context_id'])->first();
        }

        $certificateNumber = $this->buildIdsporaCertificateNumber(
            $data['activity_code'],
            $data['type_code'],
            $data['sequence'],
            $issuedAt
        );

        // Prepare logos and signatures similar to issueCertificate
        $logosBase64 = [];
        $signaturesBase64 = [];
        if ($data['context'] === 'event' && $certifiable) {
            foreach (is_array($certifiable->certificate_logo) ? $certifiable->certificate_logo : [] as $l) {
                $path = str_replace('storage/', '', (string) $l);
                if ($path !== '' && Storage::disk('public')->exists($path)) {
                    $logosBase64[] = 'data:' . (mime_content_type(Storage::disk('public')->path($path)) ?: 'application/octet-stream') . ';base64,' . base64_encode(Storage::disk('public')->get($path));
                }
            }
            foreach (is_array($certifiable->certificate_signature) ? $certifiable->certificate_signature : [] as $s) {
                $path = str_replace('storage/', '', (string) $s);
                if ($path !== '' && Storage::disk('public')->exists($path)) {
                    $signaturesBase64[] = 'data:' . (mime_content_type(Storage::disk('public')->path($path)) ?: 'application/octet-stream') . ';base64,' . base64_encode(Storage::disk('public')->get($path));
                }
            }
        }

        $roleLabelMap = [
            'SRT' => 'Peserta',
            'MC' => 'MC',
            'TRN' => 'Narasumber',
            'PNT' => 'Panitia',
            'CLB' => 'Kolaborator',
            'MOD' => 'Moderator',
            'GRD' => 'Kelulusan',
            'SPV' => 'Supervisor/penilai',
        ];

        $roleLabel = $roleLabelMap[strtoupper((string) $data['type_code'])] ?? strtoupper((string) $data['type_code']);

        $pdfData = [
            'context' => $data['context'],
            'event' => $data['context'] === 'event' ? $certifiable : null,
            'course' => $data['context'] === 'course' ? $certifiable : null,
            'user' => $trainer,
            'issuedAt' => $issuedAt,
            'certificateNumber' => $certificateNumber,
            'logosBase64' => $logosBase64,
            'signaturesBase64' => $signaturesBase64,
            'roleLabel' => $roleLabel,
            'is_preview' => true,
        ];

        return view('trainer.certificates.certificate-pdf', $pdfData);
    }

    public function revokeCertificate(TrainerCertificate $trainerCertificate)
    {
        if (!empty($trainerCertificate->file_path)) {
            $absolute = storage_path('app/' . $trainerCertificate->file_path);
            if (is_file($absolute)) {
                @unlink($absolute);
            }
        }

        $trainerCertificate->update(['status' => 'revoked']);
        return back()->with('success', 'Sertifikat trainer berhasil dicabut.');
    }

    private function buildIdsporaCertificateNumber(string $activityCode, string $typeCode, string $sequence, \Carbon\CarbonInterface $issuedAt): string
    {
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

        $monthRoman = $romanMonths[(int) $issuedAt->format('n')] ?? '';
        $year = $issuedAt->format('Y');
        $seqDigits = preg_replace('/\D+/', '', $sequence) ?: '1';
        $seq = str_pad(substr($seqDigits, -3), 3, '0', STR_PAD_LEFT);

        $activity = strtoupper(trim($activityCode ?: 'WBN'));
        $type = strtoupper(trim($typeCode ?: 'TRN'));

        return "IDSP/{$activity}/{$type}/{$seq}/{$monthRoman}/{$year}";
    }

    public function edit(User $trainer)
    {
        if ($trainer->role !== 'trainer')
            abort(404);
        return view('admin.trainer.edit', compact('trainer'));
    }

    // [UPDATED] UPDATE TRAINER (YANG ANDA CARI)
    public function update(Request $request, User $trainer)
    {
        if ($trainer->role !== 'trainer')
            abort(404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($trainer->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:30'],
            'profession' => ['nullable', 'string', 'max:100'],
            'institution' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Cek apakah password diisi (jika kosong, jangan update password)
        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($trainer->avatar && !str_starts_with($trainer->avatar, 'http')) {
                Storage::disk('public')->delete($trainer->avatar);
            }
            // Simpan yang baru
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $trainer->update($data);

        return redirect()->route('admin.trainer.index')->with('success', 'Data trainer berhasil diperbarui!');
    }

    public function destroy(User $trainer)
    {
        if ($trainer->role !== 'trainer')
            abort(404);

        if ($trainer->avatar && !str_starts_with($trainer->avatar, 'http')) {
            Storage::disk('public')->delete($trainer->avatar);
        }

        $name = $trainer->name;
        $trainer->delete();

        return redirect()->route('admin.trainer.index')->with('success', "Trainer {$name} berhasil dihapus!");
    }
}