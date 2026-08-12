<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    public const TYPE_QR_SCAN = 'qr_scan';

    public const SUBJECT_QR_CODE = 'QRCode';

    public const SOURCE_QR = 'qr';

    protected $fillable = [
        'owner_id',
        'event_type',
        'subject_type',
        'subject_id',
        'source_type',
        'source_id',
        'visitor_key',
        'country_code',
        'city',
        'referrer_host',
        'device',
        'browser',
        'os',
        'language',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
