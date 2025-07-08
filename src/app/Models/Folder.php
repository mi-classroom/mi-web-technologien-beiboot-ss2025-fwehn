<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'parent_folder_id',

        'iptc_special_instructions',
        'iptc_date_created',
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
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parentFolder(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_folder_id');
    }

    public function  childFolders(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_folder_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'folder_id');
    }
}
