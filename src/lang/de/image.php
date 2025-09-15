<?php

return [
    "_" => "Bild|Bilder",
    "name" => 'Name',
    "name_prefix" => 'Namenspräfix',
    "name_iterators" => [
        '' => '---',
        '#_?' => '#_<Präfix>',
        '?_#' => '<Präfix>_#'
    ],
    "store" => [
        "success" => ":imageName wurde erfolgreich hochgeladen!",
        "dropzone" => "Datei hierher ziehen zum Hochladen",
        "modal" => [
            "title" => "Sollen Ordner-Standards auf die neuen Bilder angewandt werden?",
            "actions" => [
                "save" => "Bilder unverändert hochladen",
                "propagate" => "Bilderdaten mit Ordner-Standards überschreiben",
                "merge" => "Fehlende Bilderdaten mit Ordner-Standards besetzen",
                "close" => "Abbrechen",
            ]
        ]
    ],
    "updateSelection" => [
        "_" => "Auswahl bearbeiten",
        "success" => "Bilder erfolgreich aktualisiert!",
    ],
    "update" => [
        "_" => "Speichern",
        "success" => "Bild erfolgreich aktualisiert!",
    ],
    "destroy" => [
        "_" => "Löschen",
        "success" => "Bild erfolgreich gelöscht!",
    ],
    "destroySelection" => [
        "success" => "Bilder erfolgreich gelöscht!",
    ],
];
