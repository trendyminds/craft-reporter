<?php

namespace trendyminds\reporter;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\models\VolumeFolder;
use craft\web\UrlManager;
use Stringy\Stringy;
use yii\base\Event;

class Reporter extends Plugin
{
	public $hasCpSection = true;

	public function init()
	{
		parent::init();

		// Setup routes for the plugin
		Event::on(
			UrlManager::class,
			UrlManager::EVENT_REGISTER_CP_URL_RULES,
			function (RegisterUrlRulesEvent $event) {
				$event->rules['reporter'] = 'reporter/default/index';
				$event->rules['reporter/exports'] = 'reporter/default/exports';
			}
		);
	}

	/**
	 * Request a specific report by name
	 *
	 * @param string $name
	 *
	 * @return array|null
	 */
	public function getReport(string $name)
	{
		return $this->getSettings()->reports[$name] ?? null;
	}

	/**
	 * Returns the VolumeFolder for exports from the config settings.
	 *
	 * @return VolumeFolder
	 */
	public function getExportPath()
	{
		$volume = Craft::$app->getVolumes()->getVolumeByHandle(
			$this->getSettings()->volume ?? ''
		);

		if (!$volume) {
			throw new \Exception('Your volume is invalid. Please make sure a volume value is set in your config and the volume handle is valid.');
		}

		$path = Stringy::create($this->getSettings()->folder)
					->trimLeft('/')
					->trimRight('/')
					->__toString();

		// Only append a trailing slash if we don't have an empty string
		if ($path) {
			$path = Stringy::create($path)->append('/')->__toString();
		}

		/** @var VolumeFolder */
		$folder = Craft::$app->getAssets()->findFolder([
			'path' => $path,
			'volumeId' => $volume->id,
		]);

		if (!$folder) {
			throw new \Exception('Your folder path is invalid. Please make sure a folder value is set in your config and the path is valid.');
		}

		return $folder;
	}

	/**
	 * Returns the data for a report given a report handle
	 *
	 * @param string $report The name of the report to fetch
	 *
	 * @return object An object containing the query and transformer to use when iterating over each row
	 */
	public function getReportData(string $report): object
	{
		$config = $this->getReport($report)();

		$elementType = $config['elementType'];
		$query = $elementType::find();
		Craft::configure($query, $config['criteria']);

		return (object) [
			'query' => $query,
			'transformer' => $config['transformer'],
		];
	}

	/**
	 * The plugin settings
	 *
	 * @return void
	 */
	protected function createSettingsModel()
	{
		return new Settings();
	}
}
