<?php

namespace trendyminds\reporter\models;

use craft\base\Model;

class Settings extends Model
{
	/**
	 * @var string The display name of the plugin across the control panel.
	 */
	public string $displayName = 'Reporter';

	/**
	 * @var string The asset volume handle for the exported reports.
	 */
	public string $volume;

	/**
	 * @var string The asset folder path for the exported reports.
	 */
	public string $folder;

	/**
	 * @var array The reports configurations.
	 */
	public array $reports = [];
}
