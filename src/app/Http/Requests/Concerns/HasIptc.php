<?php

namespace App\Http\Requests\Concerns;

trait HasIptc
{
    public static function iptcRules(): array
    {
        return [
            'iptc.iptc_object_attribute_reference' => 'nullable|string|max:255',
            'iptc.iptc_object_name' => 'nullable|string|max:255',
            'iptc.iptc_subject_reference' => 'nullable|array',
            'iptc.iptc_subject_reference.*' => 'required|string',
            'iptc.iptc_keywords' => 'nullable|array',
            'iptc.iptc_keywords.*' => 'required|string',
            'iptc.iptc_special_instructions' => 'nullable|string',
            'iptc.iptc_date_created' => 'nullable|date',
            'iptc.iptc_time_created' => 'nullable|date',
            'iptc.iptc_byline' => 'nullable|string|max:255',
            'iptc.iptc_byline_title' => 'nullable|string|max:255',
            'iptc.iptc_city' => 'nullable|string|max:255',
            'iptc.iptc_sub_location' => 'nullable|string|max:255',
            'iptc.iptc_province_state' => 'nullable|string|max:255',
            'iptc.iptc_country_primary_location_code' => 'nullable|string|max:255',
            'iptc.iptc_country_primary_location_name' => 'nullable|string|max:255',
            'iptc.iptc_original_transmission_reference' => 'nullable|string|max:255',
            'iptc.iptc_headline' => 'nullable|string|max:255',
            'iptc.iptc_credit' => 'nullable|string|max:255',
            'iptc.iptc_source' => 'nullable|string|max:255',
            'iptc.iptc_copyright_notice' => 'nullable|string|max:255',
            'iptc.iptc_caption_abstract' => 'nullable|string',
            'iptc.iptc_writer_editor' => 'nullable|string|max:255',
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
