<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class IptcDataEntry extends Model
{
    protected $table = 'iptc_data_entries';

    protected $fillable = [
        'iptc_object_attribute_reference',
        'iptc_object_name',
        'iptc_subject_reference',
        'iptc_keywords',
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
        'iptc_application_record_version',
    ];

    protected $casts = [
        'iptc_subject_reference' => 'array',
        'iptc_keywords' => 'array',
    ];

    public function iptcable(): MorphTo
    {
        return $this->morphTo();
    }
}
