<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ExifToolService
{
    protected static array $iptcMap = [
        'ObjectAttributeReference' => 'iptc_object_attribute_reference',
        'ObjectName' => 'iptc_object_name',
//        'SubjectReference' => 'iptc_subject_reference',
//        'Keywords' => 'iptc_keywords',
        'SpecialInstructions' => 'iptc_special_instructions',
        'DateCreated' => 'iptc_date_created',
        'TimeCreated' => 'iptc_time_created',
        'By-line' => 'iptc_byline',
        'By-lineTitle' => 'iptc_byline_title',
        'City' => 'iptc_city',
        'Sub-location' => 'iptc_sub_location',
        'Province-State' => 'iptc_province_state',
        'Country-PrimaryLocationCode' => 'iptc_country_primary_location_code',
        'Country-PrimaryLocationName' => 'iptc_country_primary_location_name',
        'OriginalTransmissionReference' => 'iptc_original_transmission_reference',
        'Headline' => 'iptc_headline',
        'Credit' => 'iptc_credit',
        'Source' => 'iptc_source',
        'CopyrightNotice' => 'iptc_copyright_notice',
        'Caption-Abstract' => 'iptc_caption_abstract',
        'Writer-Editor' => 'iptc_writer_editor',
        'ApplicationRecordVersion' => 'iptc_application_record_version',
    ];

    static public function read(string $filePath): array
    {
        $process = new Process([config('exiftool.path'), '-IPTC:All', '-json', $filePath]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $data = json_decode($output, true);

        $iptc = [];
        if (!empty($data[0])) {
            foreach (self::$iptcMap as $exifKey => $customKey) {
                if (isset($data[0][$exifKey])) {
                    $iptc[$customKey] = $data[0][$exifKey];
                }
            }
        }

        return $iptc;
    }

    static public function write(string $filePath, array $iptcData): void
    {
        $command = [config('exiftool.path')];

        foreach (self::$iptcMap as $exifKey => $customKey) {
            if (isset($iptcData[$customKey]) && $iptcData[$customKey] !== null) {
                $command[] = "-IPTC:{$exifKey}={$iptcData[$customKey]}";
            }
        }

        $command[] = $filePath;

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $backupFile = $filePath . '_original';
        if (file_exists($backupFile)) {
            unlink($backupFile);
        }
    }
}
