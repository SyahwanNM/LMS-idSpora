<?php

return [
    // Optional override for ffprobe binary location; set in .env as FFPROBE_PATH
    // Example (Windows): FFPROBE_PATH="C:\\ffmpeg\\bin\\ffprobe.exe"
    // Example (Linux): FFPROBE_PATH="/usr/local/bin/ffprobe"
    'ffprobe_path' => env('FFPROBE_PATH', ''),
];
