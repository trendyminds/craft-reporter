<?php

namespace trendyminds\reporter\console\controllers;

Use Craft;
use craft\console\Controller;
use trendyminds\reporter\jobs\ExportJob;
use trendyminds\reporter\Reporter;
use yii\helpers\Console;

class ReportController extends Controller
{
	/**
	 * @var string|null The type of sync to run
	 */
	public $handle = null;

	public function options($actionID)
	{
		$options = parent::options($actionID);
		$options[] = 'handle';

		return $options;
	}

	/**
	 *  Run Reporter from the command line. Must pass in --handle=<report Craft handle>
	 */
	public function actionIndex()
	{
		$report = Reporter::getInstance()->getReport($this->handle);

		if(!$report){
			$this->stderr("You must specify the handle of an existing report to export. Consult config/reporter.php to ensure you have created one and you are using the handle." . PHP_EOL, Console::FG_RED);
			die();
		}

		$report = $report();
		$this->stdout("Running report " . $report['name'] . "." . PHP_EOL, Console::FG_BLUE);

		Craft::$app->getQueue()->push(
			new ExportJob([
				'handle' => $this->handle,
				'name' => $report['name'],
			])
		);
	}
}
