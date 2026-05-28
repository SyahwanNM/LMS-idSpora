<?php
$trainers = App\Models\User::where('role', 'trainer')->get();
foreach($trainers as $t) {
    $c = App\Models\Course::where('trainer_id', $t->id)->first();
    $e = App\Models\Event::where('trainer_id', $t->id)->first();
    
    if($c) {
        App\Models\ManualPayment::create([
            'order_id' => 'DUMMY-BUY-'.$t->id.'-'.time(),
            'user_id' => 2,
            'course_id' => $c->id,
            'amount' => 500000,
            'status' => 'settled',
            'method' => 'manual_transfer',
            'metadata' => ['description' => 'Test sync']
        ]);
        App\Models\TrainerPayment::create([
            'user_id' => $t->id,
            'type' => 'course_payout',
            'course_id' => $c->id,
            'trainer_name' => $t->name,
            'title' => 'Pencairan Manual',
            'amount' => 250000,
            'status' => 'approved',
            'payment_method' => 'transfer',
            'payment_date' => now(),
            'notes' => 'Test inject'
        ]);
    } elseif ($e) {
        App\Models\ManualPayment::create([
            'order_id' => 'DUMMY-BUY-E'.$t->id.'-'.time(),
            'user_id' => 2,
            'event_id' => $e->id,
            'amount' => 500000,
            'status' => 'settled',
            'method' => 'manual_transfer',
            'metadata' => ['description' => 'Test sync']
        ]);
        App\Models\TrainerPayment::create([
            'user_id' => $t->id,
            'type' => 'event_fee',
            'event_id' => $e->id,
            'trainer_name' => $t->name,
            'title' => 'Pencairan Manual',
            'amount' => 250000,
            'status' => 'approved',
            'payment_method' => 'transfer',
            'payment_date' => now(),
            'notes' => 'Test inject'
        ]);
    }
}
echo "Injected for all possible trainers!\n";
