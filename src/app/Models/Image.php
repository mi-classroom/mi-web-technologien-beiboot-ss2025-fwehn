<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Image extends Model
{
    protected $fillable = [
        'name',
        'width',
        'height',
        'user_id',
        'original_path',
        'folder_id',
    ];

    protected $appends = ['preview_url', 'fill_percent'];

    public function getPreviewUrlAttribute(): string
    {
        return route('images.preview', ['image' => $this->id]);
    }

    public function getFillPercentAttribute(): int
    {
        $iptc = $this->iptc;

        if (!$iptc) return 0;

        $iptcFields = collect($iptc->getAttributes())->filter(fn($value, $key) => str_starts_with($key, 'iptc_'));

        $total = $iptcFields->count();

        if ($total === 0) return 0;

        $filled = $iptcFields->filter(fn($value) => !is_null($value) && $value !== '')->count();

        return (int)round(($filled / $total) * 100);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function iptc(): MorphOne
    {
        return $this->morphOne(IptcDataEntry::class, 'iptcable');
    }

}
