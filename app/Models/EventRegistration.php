<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\ManualPayment;
use App\Models\PaymentProof;

class EventRegistration extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'status',
        'attendance_status',
        'attended_at',
        'attendance_scan_qr',
        'registration_code',
        'certificate_number',
        'certificate_issued_at',
        'feedback_text',
        'feedback_submitted_at',
        'total_price', 
        'payment_proof',
        'payment_verified_at',
        'payment_verified_by',
        'payment_url',
        'rejection_reason',
        'university_origin',
        'study_program',
        'position',
        'submission_path',
        'submission_uploaded_at',
        'submission_status',
        'submission_path_2',
        'submission_2_uploaded_at',
        'submission_notes',
        'stage2_payment_status',
        'stage2_payment_at',
        'team_id',
        'is_team_leader',
        'full_name',
        'whatsapp_number',
        'team_name',
        'institution_location',
        'info_source',
        'educational_background',
    ];

    protected $casts = [
        'certificate_issued_at' => 'datetime',
        'feedback_submitted_at' => 'datetime',
        'attended_at' => 'datetime',
        'attendance_scan_qr' => 'datetime',
        'submission_uploaded_at' => 'datetime',
        'submission_2_uploaded_at' => 'datetime',
        'stage2_payment_at' => 'datetime',
    ];

    public function getInvoiceUrlAttribute()
    {
        // Check Manual
        $manual = ManualPayment::where('event_id', $this->event_id)
            ->where('user_id', $this->user_id)
            ->where('status', 'settled')
            ->first();

        if ($manual) {
            return $manual->order_id ? route('invoice.manual', $manual->order_id) : null;
        }

        return null;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function event(){
        return $this->belongsTo(Event::class);
    }

    public function team(){
        return $this->belongsTo(Team::class);
    }

    public function paymentProofs()
    {
        return $this->hasMany(PaymentProof::class, 'event_registration_id');
    }

    public function dailyAttendances()
    {
        return $this->hasMany(EventDailyAttendance::class, 'event_registration_id');
    }

    protected static function booted()
    {
        static::creating(function ($registration) {
            if ($registration->team_id && !$registration->is_team_leader) {
                $leaderReg = self::where('team_id', $registration->team_id)
                    ->where('is_team_leader', true)
                    ->first();
                
                if ($leaderReg) {
                    $registration->status = $leaderReg->status;
                    $registration->submission_path = $leaderReg->submission_path;
                    $registration->submission_uploaded_at = $leaderReg->submission_uploaded_at;
                    $registration->submission_status = $leaderReg->submission_status;
                    $registration->submission_notes = $leaderReg->submission_notes;
                    $registration->submission_path_2 = $leaderReg->submission_path_2;
                    $registration->submission_2_uploaded_at = $leaderReg->submission_2_uploaded_at;
                    $registration->stage2_payment_status = $leaderReg->stage2_payment_status;
                    $registration->stage2_payment_at = $leaderReg->stage2_payment_at;
                }
            }
        });

        static::updated(function ($registration) {
            if ($registration->team_id && $registration->is_team_leader) {
                $updates = [];
                
                if ($registration->isDirty('status')) {
                    $updates['status'] = $registration->status;
                }
                
                if ($registration->isDirty('submission_status')) {
                    $updates['submission_status'] = $registration->submission_status;
                }
                
                if ($registration->isDirty('submission_notes')) {
                    $updates['submission_notes'] = $registration->submission_notes;
                }
                
                if ($registration->isDirty('stage2_payment_status')) {
                    $updates['stage2_payment_status'] = $registration->stage2_payment_status;
                }
                
                if ($registration->isDirty('stage2_payment_at')) {
                    $updates['stage2_payment_at'] = $registration->stage2_payment_at;
                }

                if ($registration->isDirty('submission_path')) {
                    $updates['submission_path'] = $registration->submission_path;
                }

                if ($registration->isDirty('submission_uploaded_at')) {
                    $updates['submission_uploaded_at'] = $registration->submission_uploaded_at;
                }

                if ($registration->isDirty('submission_path_2')) {
                    $updates['submission_path_2'] = $registration->submission_path_2;
                }

                if ($registration->isDirty('submission_2_uploaded_at')) {
                    $updates['submission_2_uploaded_at'] = $registration->submission_2_uploaded_at;
                }
                
                if (!empty($updates)) {
                    // Update other registrations in the same team
                    self::where('team_id', $registration->team_id)
                        ->where('id', '!=', $registration->id)
                        ->update($updates);
                }

                if ($registration->isDirty('status')) {
                    // Update team status
                    $team = $registration->team;
                    if ($team && $team->status !== $registration->status) {
                        $team->status = $registration->status;
                        $team->save();
                    }
                }
            }
        });
    }
}
