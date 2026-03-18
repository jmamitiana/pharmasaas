<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    public function createBackup(?int $tenantId = null): Backup
    {
        $filename = 'backup-' . now()->format('Y-m-d-H-i-s') . '.gz';
        
        $backup = Backup::create([
            'tenant_id' => $tenantId,
            'filename' => $filename,
            'path' => 'backups/',
            'size' => 0,
            'status' => 'pending',
        ]);

        try {
            $path = storage_path('app/backups/' . $filename);
            
            if (!File::exists(storage_path('app/backups'))) {
                File::makeDirectory(storage_path('app/backups'), 0755, true);
            }

            Artisan::call('backup:run', [
                '--filename' => $filename,
                '--only-db' => true,
            ]);

            if (File::exists($path)) {
                $backup->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'size' => File::size($path),
                ]);
            } else {
                $backup->update([
                    'status' => 'failed',
                    'error_message' => 'Backup file not created',
                ]);
            }
        } catch (\Exception $e) {
            $backup->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }

        return $backup;
    }

    public function restoreBackup(Backup $backup): bool
    {
        if ($backup->status !== 'completed') {
            return false;
        }

        try {
            $path = storage_path('app/backups/' . $backup->filename);
            
            if (!File::exists($path)) {
                throw new \Exception('Backup file not found');
            }

            Artisan::call('backup:restore', [
                '--filename' => $backup->filename,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteOldBackups(int $days = 30): void
    {
        $backups = Backup::where('created_at', '<', now()->subDays($days))->get();
        
        foreach ($backups as $backup) {
            $path = storage_path('app/backups/' . $backup->filename);
            if (File::exists($path)) {
                File::delete($path);
            }
            $backup->delete();
        }
    }

    public function getBackupPath(): string
    {
        return storage_path('app/backups');
    }
}
