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


//      "ObjectAttributeReference": "000:Actuality",
//      "ObjectName": "The Title (ref2024.1)",
//      "SubjectReference": ["IPTC:10020231","IPTC:20020231","IPTC:30020231"],
//      "Keywords": ["Keyword1ref2024.1","Keyword2ref2024.1","Keyword3ref2024.1"],
//      "SpecialInstructions": "An Instruction (ref2024.1)",
//      "DateCreated": "2024:03:22",
//      "TimeCreated": "00:23:02+00:00",
//      "By-line": "Creator1 (ref2024.1)",
//      "By-lineTitle": "Creator's Job Title  (ref2024.1)",
//      "City": "City (Core) (ref2024.1)",
//      "Sub-location": "Sublocation (Core) (ref2024.1)",
//      "Province-State": "Province/State(Core)(ref2024.1)",
//      "Country-PrimaryLocationCode": "R23",
//      "Country-PrimaryLocationName": "Country (Core) (ref2024.1)",
//      "OriginalTransmissionReference": "Job Id (ref2024.1)",
//      "Headline": "The Headline (ref2024.1)",
//      "Credit": "Credit Line (ref2024.1)",
//      "Source": "Source (ref2024.1)",
//      "CopyrightNotice": "Copyright (Notice) 2024.1 IPTC - www.iptc.org  (ref2024.1)",
//      "Caption-Abstract": "The description aka caption (ref2024.1)",
//      "Writer-Editor": "Description Writer (ref2024.1)",
//      "ApplicationRecordVersion": 4


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
