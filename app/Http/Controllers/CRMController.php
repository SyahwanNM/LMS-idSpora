<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CRMController extends Controller
{
    /**
     * Display CRM Dashboard
     */
    public function dashboard()
    {
        // Only admin can access
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        // Get statistics
        $totalCustomers = User::where('role', '!=', 'admin')->count();
        $activeCustomers = User::where('role', '!=', 'admin')
            ->where(function($query) {
                $query->whereHas('eventRegistrations', function($q) {
                    $q->where('status', 'active');
                })
                ->orWhereHas('enrollments', function($q) {
                    $q->where('status', 'active');
                });
            })
            ->count();
        
        $totalRegistrations = EventRegistration::where('status', 'active')->count();
        $totalEnrollments = Enrollment::where('status', 'active')->count();
        
        // Recent registrations
        $recentRegistrations = EventRegistration::with(['user', 'event'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Top events by registration
        $topEvents = Event::withCount(['registrations' => function($query) {
            $query->where('status', 'active');
        }])
        ->orderBy('registrations_count', 'desc')
        ->limit(5)
        ->get();

        return view('admin.crm.dashboard', compact(
            'totalCustomers',
            'activeCustomers',
            'totalRegistrations',
            'totalEnrollments',
            'recentRegistrations',
            'topEvents'
        ));
    }

    /**
     * Display list of customers
     */
    public function customers(Request $request)
    {
        // Only admin can access
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        $query = User::where('role', '!=', 'admin')
            ->withCount(['eventRegistrations', 'enrollments']);

        // Search functionality
        if($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.crm.customers.index', compact('customers'));
    }

    /**
     * Show customer detail
     */
    public function showCustomer(User $customer)
    {
        // Only admin can access
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        // Prevent viewing admin as customer
        if($customer->role === 'admin'){
            abort(404);
        }

        // Load customer data with relationships
        $customer->load([
            'eventRegistrations.event',
            'enrollments.course'
        ]);

        $registrations = $customer->eventRegistrations()
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->get();

        $enrollments = $customer->enrollments()
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.crm.customers.show', compact('customer', 'registrations', 'enrollments'));
    }

    /**
     * Edit customer
     */
    public function editCustomer(User $customer)
    {
        // Only admin can access
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        // Prevent editing admin as customer
        if($customer->role === 'admin'){
            abort(404);
        }

        return view('admin.crm.customers.edit', compact('customer'));
    }

    /**
     * Update customer
     */
    public function updateCustomer(Request $request, User $customer)
    {
        // Only admin can access
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        // Prevent editing admin as customer
        if($customer->role === 'admin'){
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'bio' => 'nullable|string|max:1000',
            'role' => 'required|in:user,reseller,trainer',
        ]);

        $customer->update($request->only(['name', 'email', 'phone', 'website', 'bio', 'role']));

        return redirect()->route('admin.crm.customers.show', $customer)
            ->with('success', 'Data customer berhasil diperbarui');
    }
}

