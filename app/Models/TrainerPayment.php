<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerPayment extends Model
{
    protected $fillable = [
        'user_id', 'type', 'event_id', 'course_id', 'trainer_name',
        'title', 'amount', 'payment_date', 'payment_method',
        'proof_file', 'notes', 'status', 'rejected_reason',
        'salary_slip', 'proof_of_payment', 'month', 'year',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    /** The trainer (user) receiving the payment */
    public function trainer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Related event (for event_fee type) */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /** Related course (for course_payout type) */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /* ── Scopes ─────────────────────────────── */
    public function scopePending($q)     { return $q->where('status', 'pending'); }
    public function scopeApproved($q)    { return $q->where('status', 'approved'); }
    public function scopeCoursePayout($q){ return $q->where('type', 'course_payout'); }
    public function scopeEventFee($q)    { return $q->where('type', 'event_fee'); }
}
