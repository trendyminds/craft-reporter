# <img src="src/icon.svg" height="32" width="32"> Reporter

📊 Export Craft data as CSVs in a snap!

<img src="docs/reports.png" width="1200" alt="An example of the reports screen before a user has initiated an export">

## ⚡️ Features

- 🏎 Reports are processed with [Query Batching](https://www.yiiframework.com/doc/api/2.0/yii-db-query#batch()-detail), making exports run quickly and without exhausting your memory limit
- 🧘 Inspired by [Element API](https://github.com/craftcms/element-api), create reports with a simple and familiar structure
- 📦 Reports stored using Asset volumes so you can host reports locally or on a cloud-based service like Amazon S3
- 🖥 Process reports via the CLI using `php craft reporter/report --handle=myReport` where `myReport` is the key of a specific report in the `reports` array found in `config/reporter.php`.

## 📦 Installing

Install Reporter one of two ways:

- [Install via Craft's Plugin Store](https://plugins.craftcms.com/reporter)
- Run `composer require trendyminds/craft-reporter` and enable the plugin from "Settings > Plugins"

## 🔌 Setup

To setup reports, create a `reporter.php` file in `config/`. Below is an example config file.

Example Config
```php
<?php

use craft\elements\Entry;
use craft\elements\Asset;

return [
    // The name to use throughout the control panel (defaults to "Reporter")
    'displayName' => 'Reports',

    // The asset volume handle where your reports should be saved
    // NOTE: Your reports are publicly accessible if your volume has "Assets in this volume have public URLs" enabled
    'volume' => 'uploads',

    // An optional folder path if you would like to nest the reports in a specific directory
    'folder' => 'resources/reports',

		// An optional batch size to use when processing reports (defaults to 100)
		'batchSize' => 100,

    // An array of reports to produce
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

        'allImages' => function () {
            return [
                'name' => 'Uploaded Images',
                'description' => 'A list of all images uploaded into Craft',
                'elementType' => Asset::class,
                'criteria' => [
                    'kind' => 'image'
                ],
                'transformer' => function (Asset $asset) {
                    // Skip example
                    // Ignore assets that have an even number for an ID
                    if ($asset->id % 2 === 0) {
                        return [];
                    }

                    return [
                        "id" => $asset->id,
                        "title" => $asset->title,
                        "filename" => $asset->filename,
                    ];
                }
            ];
        },
    ]
];
```

<img src="docs/export.png" width="1200" alt="The exports screen that is displayed when you have not exported any reports yet.">

## 🤝 Contributing

If you would like to contribute to Reporter we tried to make it as easy as possible:

1. Clone the repo
2. Run `npm i` to install the Node dependencies
3. Run `npm start` to begin the watch task
4. Make your changes
5. Run `npm run build` to compile and minify the CSS and JS
6. Submit a PR!
