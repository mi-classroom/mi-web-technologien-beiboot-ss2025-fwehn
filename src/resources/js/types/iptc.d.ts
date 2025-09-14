interface Iptc {
    iptc_object_attribute_reference?: string | null;
    iptc_object_name?: string | null;
    iptc_subject_reference?: string[] | null;
    iptc_keywords?: string[] | null;
    iptc_special_instructions?: string | null;
    iptc_date_created?: string | null;
    iptc_time_created?: string | null;
    iptc_byline?: string | null;
    iptc_byline_title?: string | null;
    iptc_city?: string | null;
    iptc_sub_location?: string | null;
    iptc_province_state?: string | null;
    iptc_country_primary_location_code?: string | null;
    iptc_country_primary_location_name?: string | null;
    iptc_original_transmission_reference?: string | null;
    iptc_headline?: string | null;
    iptc_credit?: string | null;
    iptc_source?: string | null;
    iptc_copyright_notice?: string | null;
    iptc_caption_abstract?: string | null;
    iptc_writer_editor?: string | null;
    iptc_application_record_version?: number | null;
}

interface IptcForm {
    iptc_object_attribute_reference?: string | null;
    iptc_object_name?: string | null;
    iptc_subject_reference?: string[] | null;
    iptc_keywords?: string[] | null;
    iptc_special_instructions?: string | null;
    iptc_date_created?: Date | null;
    iptc_time_created?: Date | null;
    iptc_byline?: string | null;
    iptc_byline_title?: string | null;
    iptc_city?: string | null;
    iptc_sub_location?: string | null;
    iptc_province_state?: string | null;
    iptc_country_primary_location_code?: string | null;
    iptc_country_primary_location_name?: string | null;
    iptc_original_transmission_reference?: string | null;
    iptc_headline?: string | null;
    iptc_credit?: string | null;
    iptc_source?: string | null;
    iptc_copyright_notice?: string | null;
    iptc_caption_abstract?: string | null;
    iptc_writer_editor?: string | null;
    iptc_application_record_version?: number | null;
}
