<?php

namespace App\Http\Requests\Concerns;

trait HasIptc
{
    public static function iptcRules(): array
    {
        return [
            'iptc.iptc_object_attribute_reference' => 'nullable|string',
            'iptc.iptc_object_name' => 'nullable|string',
            'iptc.iptc_subject_reference' => 'nullable|string',
            'iptc.iptc_special_instructions' => 'nullable|string',
            'iptc.iptc_date_created' => 'nullable', // TODO Date
            'iptc.iptc_time_created' => 'nullable', // TODO Time
            'iptc.iptc_byline' => 'nullable|string',
            'iptc.iptc_byline_title' => 'nullable|string',
            'iptc.iptc_city' => 'nullable|string',
            'iptc.iptc_sub_location' => 'nullable|string',
            'iptc.iptc_province_state' => 'nullable|string',
            'iptc.iptc_country_primary_location_code' => 'nullable|string',
            'iptc.iptc_country_primary_location_name' => 'nullable|string',
            'iptc.iptc_original_transmission_reference' => 'nullable|string',
            'iptc.iptc_headline' => 'nullable|string',
            'iptc.iptc_credit' => 'nullable|string',
            'iptc.iptc_source' => 'nullable|string',
            'iptc.iptc_copyright_notice' => 'nullable|string',
            'iptc.iptc_caption_abstract' => 'nullable|string',
            'iptc.iptc_writer_editor' => 'nullable|string',
            'iptc.iptc_application_record_version' => 'nullable|integer'
        ];
    }

    public static function iptcAttributes(): array
    {
        $translations = trans('iptc');

        $mapped = [];
        foreach ($translations as $key => $label) {
            $mapped["iptc.$key"] = $label;
        }

        return $mapped;
    }
}
