<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use LogicException;

class QRCode extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const DESTINATION_EXTERNAL = 'external';

    public const DESTINATION_BIOLINK = 'biolink';

    public const DESTINATION_SHORTLINK = 'shortlink';

    public $table = 'qrcodes';

    protected $fillable = [
        'user_id',
        'link_id',
        'project_id',
        'name',
        'public_code',
        'is_dynamic',
        'is_active',
        'destination_type',
        'destination_url',
        'destination_link_id',
        'qr_type',
        'content',
        'img_data',
    ];

    protected $casts = [
        'is_dynamic' => 'boolean',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(function (QRCode $qrCode) {
            if ($qrCode->isDirty('public_code')) {
                $originalCode = $qrCode->getOriginal('public_code');
                if ($originalCode !== null && $originalCode !== '') {
                    throw new LogicException('QR public_code is immutable once assigned.');
                }
            }

            // Dynamic QR encoded URL must stay stable so printed codes keep working.
            if ($qrCode->is_dynamic && $qrCode->isDirty('content')) {
                $originalContent = $qrCode->getOriginal('content');
                if (is_string($originalContent) && $originalContent !== '') {
                    throw new LogicException('Dynamic QR content is immutable once assigned.');
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function destinationLink(): BelongsTo
    {
        return $this->belongsTo(Link::class, 'destination_link_id');
    }
}
