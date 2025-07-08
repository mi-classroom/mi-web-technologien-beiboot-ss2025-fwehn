<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    protected $fillable = [
        'name',
        'width',
        'height',
        'user_id',
        'original_path',
        'folder_id',

        'iptc_object_attribute_reference',
        'iptc_object_name',
        'iptc_subject_reference',
        'iptc_special_instructions',
        'iptc_date_created',
        'iptc_time_created',
        'iptc_byline',
        'iptc_byline_title',
        'iptc_city',
        'iptc_sub_location',
        'iptc_province_state',
        'iptc_country_primary_location_code',
        'iptc_country_primary_location_name',
        'iptc_original_transmission_reference',
        'iptc_headline',
        'iptc_credit',
        'iptc_source',
        'iptc_copyright_notice',
        'iptc_caption_abstract',
        'iptc_writer_editor',
        'iptc_application_record_version'
    ];

    protected $appends = ['preview_url', 'iptc_fill_percent'];

    public function getPreviewUrlAttribute(): string
    {
        return route('images.preview', ['image' => $this->id]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function iptc(): array
    {
        return collect($this->attributes)
            ->filter(fn($_, $key) => str_starts_with($key, 'iptc_'))
            ->toArray();
    }

    public function getIptcFillPercentAttribute(): int
    {
        $iptcFields = collect($this->attributes)
            ->filter(fn($_, $key) => str_starts_with($key, 'iptc_'));

        $total = $iptcFields->count();

        if ($total === 0) {
            return 0;
        }

        $filled = $iptcFields->filter(function ($value) {
            return !is_null($value) && $value !== '';
        })->count();

        return (int)round(($filled / $total) * 100);
    }
}
