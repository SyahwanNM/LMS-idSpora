<?php
$notifications = App\Models\TrainerNotification::where('type', 'course_invitation')->get();
foreach($notifications as $n) {
    $data = $n->data ?? [];
    if (!isset($data['scheme_type'])) {
        $contrib = $data['contribution_scheme'] ?? '';
        $type = 0;
        if ($contrib === 'e2e') $type = 1;
        elseif ($contrib === 'module_video') $type = 2;
        elseif ($contrib === 'video_only') $type = 3;
        
        if ($type > 0) {
            $data['scheme_type'] = $type;
            $n->data = $data;
            $n->save();
        }
    }
}
echo 'Done';
