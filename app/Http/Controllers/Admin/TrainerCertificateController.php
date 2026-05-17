<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Event;
use App\Models\TrainerCertificate;
use App\Models\TrainerCertificateAsset;
use App\Models\User;
use App\Services\TrainerCertificateService;
use Illuminate\Http\Request;

class TrainerCertificateController extends Controller
{
    public function __construct(
        protected TrainerCertificateService $certificateService
    ) {
    }

    public function index(Request $request)
    {
        $certificates = TrainerCertificate::query()
            ->with([
                'trainer',
                'certifiable',
                'issuer',
            ])
            ->latest()
            ->paginate(20);

        return view('admin.trainer-certificates.index', [
            'certificates' => $certificates,
        ]);
    }
}