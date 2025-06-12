<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_folder_id')->nullable()->constrained('folders')->onDelete('cascade');

            $table->text('iptc_special_instructions')->nullable();
            $table->date('iptc_date_created')->nullable();
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

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folders');
    }
};
