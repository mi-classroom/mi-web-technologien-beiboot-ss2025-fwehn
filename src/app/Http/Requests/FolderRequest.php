<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FolderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => "required|string",

            'iptc_object_attribute_reference' => 'nullable|string',
            'iptc_object_name' => 'nullable|string',
            'iptc_subject_reference' => 'nullable|string',
            'iptc_special_instructions' => 'nullable|string',
            'iptc_date_created' => 'nullable', // TODO Date
            'iptc_time_created' => 'nullable', // TODO Time
            'iptc_byline' => 'nullable|string',
            'iptc_byline_title' => 'nullable|string',
            'iptc_city' => 'nullable|string',
            'iptc_sub_location' => 'nullable|string',
            'iptc_province_state' => 'nullable|string',
            'iptc_country_primary_location_code' => 'nullable|string',
            'iptc_country_primary_location_name' => 'nullable|string',
            'iptc_original_transmission_reference' => 'nullable|string',
            'iptc_headline' => 'nullable|string',
            'iptc_credit' => 'nullable|string',
            'iptc_source' => 'nullable|string',
            'iptc_copyright_notice' => 'nullable|string',
            'iptc_caption_abstract' => 'nullable|string',
            'iptc_writer_editor' => 'nullable|string',
            'iptc_application_record_version' => 'nullable|integer'
        ];
    }
}
