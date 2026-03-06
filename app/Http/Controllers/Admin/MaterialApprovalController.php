<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialApprovalController extends Controller
{
    /**
     * Display queue of materials pending review
     */
    public function index(Request $request)
    {
        $query = Course::with(['trainer', 'category', 'modules'])
            ->where('status', 'pending_review')
            ->withCount('modules');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('trainer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Sort functionality
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $pendingMaterials = $query->paginate(15);

        // Statistics
        $totalPending = Course::where('status', 'pending_review')->count();
        $totalApproved = Course::where('status', 'approved')->count();
        $totalRejected = Course::where('status', 'rejected')->count();

        return view('admin.material.approvals', compact(
            'pendingMaterials',
            'totalPending',
            'totalApproved',
            'totalRejected'
        ));
    }

    /**
     * Display specific material for review with preview
     */
    public function show(Course $material)
    {
        // Load relationships
        $material->load([
            'trainer',
            'category',
            'modules.quizQuestions',
            'reviews'
        ]);

        return view('admin.material.show', compact('material'));
    }

    /**
     * Approve material
     */
    public function approve(Course $material)
    {
        $material->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'rejection_reason' => null,
            'rejected_at' => null,
        ]);

        return redirect()
            ->route('admin.material.approvals')
            ->with('success', "Materi \"{$material->name}\" berhasil disetujui dan dipublikasikan!");
    }

    /**
     * Reject material with reason
     */
    public function reject(Request $request, Course $material)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ]);

        $material->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
            'approved_by' => Auth::id(), // Track who rejected it
        ]);

        return redirect()
            ->route('admin.material.approvals')
            ->with('success', "Materi \"{$material->name}\" ditolak dan catatan revisi telah dikirim ke trainer.");
    }

    /**
     * Show all approved materials
     */
    public function approved(Request $request)
    {
        $query = Course::with(['trainer', 'category'])
            ->where('status', 'approved')
            ->withCount('modules');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('trainer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $approvedMaterials = $query->orderBy('approved_at', 'desc')->paginate(15);

        return view('admin.material.approved', compact('approvedMaterials'));
    }

    /**
     * Show all rejected materials
     */
    public function rejected(Request $request)
    {
        $query = Course::with(['trainer', 'category'])
            ->where('status', 'rejected')
            ->withCount('modules');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('trainer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $rejectedMaterials = $query->orderBy('rejected_at', 'desc')->paginate(15);

        return view('admin.material.rejected', compact('rejectedMaterials'));
    }
}

