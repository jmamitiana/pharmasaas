<?php

return [
    'path' => env('BACKUP_PATH', 'storage/backups'),
    'schedule' => env('BACKUP_SCHEDULE', 'daily'),
    'keep_days' => 30,
    'compression' => 'gzip',
];
