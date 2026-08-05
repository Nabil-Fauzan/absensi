<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'date', 'check_in', 'check_out', 'status', 'notes', 'latitude_in', 'longitude_in', 'latitude_out', 'longitude_out', 'work_mode', 'minutes_late', 'approval_status', 'rejection_reason', 'is_suspicious', 'spoof_reason', 'is_ip_fallback'])]
class Attendance extends Model
{
    /**
     * Get the user that owns the attendance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
