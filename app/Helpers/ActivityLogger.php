<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log an activity in the system.
     *
     * @param string $action
     * @param string|null $module
     * @param array|null $data
     * @return void
     */
    public static function log(string $action, string $module = null, $model = null, $id = null, array $data = null): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'model' => $model,
            'row_id' => $id,
            'data' => $data ? json_encode($data) : null,
            'ip_address' => request()->ip(),
        ]);
    }
}
