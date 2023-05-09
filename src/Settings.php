<?php

namespace trendyminds\reporter;

use craft\base\Model;

class Settings extends Model
{
    /**
     * @var string The display name of the plugin across the control panel.
     */
    public $displayName = 'Reporter';

    /**
     * @var string The asset volume handle for the exported reports.
     */
    public $volume;

    /**
     * @var string The asset folder path for the exported reports.
     */
    public $folder;

    /**
     * @var array The reports configurations.
     */
    public $reports = [];
}
