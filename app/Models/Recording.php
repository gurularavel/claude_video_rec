<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recording extends Model
{
    protected $fillable = [
        'support_session_id',
        'twilio_recording_sid',
        'recording_url',
        'recording_status',
        'duration',
        'size',
        'format',
        'recording_started_at',
        'recording_completed_at',
    ];

    protected $casts = [
        'recording_started_at' => 'datetime',
        'recording_completed_at' => 'datetime',
    ];

    public function supportSession(): BelongsTo
    {
        return $this->belongsTo(SupportSession::class);
    }
}
