<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function index()
    {
        $tenantId = Auth::user()->tenant_id;
        $backups = Backup::where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('backups.index', compact('backups'));
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id;
        
        $backup = Backup::create([
            'tenant_id' => $tenantId,
            'filename' => 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql',
            'path' => 'storage/backups/',
            'size' => 0,
            'status' => 'pending',
        ]);

        try {
            $filename = $backup->filename;
            $path = storage_path('app/backups/' . $filename);
            
            if (!File::exists(storage_path('app/backups'))) {
                File::makeDirectory(storage_path('app/backups'), 0755, true);
            }

            Artisan::call('backup:run', ['--filename' => $filename]);
            
            $backup->update([
                'status' => 'completed',
                'completed_at' => now(),
                'size' => File::size($path) ?? 0,
            ]);

            return redirect()->route('backups.index')->with('success', __('Backup created successfully'));
        } catch (\Exception $e) {
            $backup->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->route('backups.index')->with('error', __('Backup failed: ') . $e->getMessage());
        }
    }

    public function download(Backup $backup)
    {
        $this->authorizeTenant($backup);
        
        $path = storage_path('app/backups/' . $backup->filename);
        
        if (!File::exists($path)) {
            return redirect()->route('backups.index')->with('error', __('Backup file not found'));
        }

        return response()->download($path);
    }

    public function restore(Backup $backup)
    {
        $this->authorizeTenant($backup);
        
        if ($backup->status !== 'completed') {
            return redirect()->route('backups.index')->with('error', __('Cannot restore incomplete backup'));
        }

        try {
            $path = storage_path('app/backups/' . $backup->filename);
            Artisan::call('backup:restore', ['--filename' => $backup->filename]);
            
            return redirect()->route('backups.index')->with('success', __('Backup restored successfully'));
        } catch (\Exception $e) {
            return redirect()->route('backups.index')->with('error', __('Restore failed: ') . $e->getMessage());
        }
    }

    public function destroy(Backup $backup)
    {
        $this->authorizeTenant($backup);
        
        $path = storage_path('app/backups/' . $backup->filename);
        if (File::exists($path)) {
            File::delete($path);
        }

        $backup->delete();

        return redirect()->route('backups.index')->with('success', __('Backup deleted successfully'));
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
    }
}
