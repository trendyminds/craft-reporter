# Reporter

Example Config
```php
<?php

use craft\elements\Entry;

return [
    // The name to use throughout the control panel (defaults to "Reporter")
    'displayName' => 'Reports',

    // The asset volume and folder to use when exporting reports
    'volume' => 'uploads',
    'folder' => 'resources/reports',

    // The list of reports to produce
    'reports' => [
        'pages' => function () {
            return [
                'name' => 'All Pages',
                'description' => 'A simple export of all the pages on the site.',
                'elementType' => Entry::class,
                'criteria' => [
                    'section' => 'pages'
                ],
                'transformer' => function (Entry $entry) {
                    return [
                        "id" => $entry->id,
                        "title" => $entry->title,
                        "url" => $entry->url,
                    ];
                }
            ];
        },
    ]
];
```
