<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('iptc_data_entries', function (Blueprint $table) {
            $table->id();

            $table->morphs('iptcable');

            // IPTC Felder
            $table->string('iptc_object_attribute_reference')->nullable();
            $table->string('iptc_object_name')->nullable();
            $table->json('iptc_subject_reference')->nullable();
            $table->json('iptc_keywords')->nullable();
            $table->text('iptc_special_instructions')->nullable();
            $table->date('iptc_date_created')->nullable();
            $table->time('iptc_time_created')->nullable();
            $table->string('iptc_byline')->nullable();
            $table->string('iptc_byline_title')->nullable();
            $table->string('iptc_city')->nullable();
            $table->string('iptc_sub_location')->nullable();
            $table->string('iptc_province_state')->nullable();
            $table->string('iptc_country_primary_location_code')->nullable();
            $table->string('iptc_country_primary_location_name')->nullable();
            $table->string('iptc_original_transmission_reference')->nullable();
            $table->string('iptc_headline')->nullable();
            $table->string('iptc_credit')->nullable();
            $table->string('iptc_source')->nullable();
            $table->string('iptc_copyright_notice')->nullable();
            $table->text('iptc_caption_abstract')->nullable();
            $table->string('iptc_writer_editor')->nullable();
            $table->integer('iptc_application_record_version')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iptc_data_entries');
    }
};
