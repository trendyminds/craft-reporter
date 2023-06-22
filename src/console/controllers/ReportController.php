<?php

namespace trendyminds\reporter\console\controllers;

use Craft;
use craft\console\Controller;
use trendyminds\reporter\jobs\ExportJob;
use trendyminds\reporter\Reporter;
use yii\console\ExitCode;
use yii\helpers\Console;

class ReportController extends Controller
{
    /**
     * @var string The handle of the report to export
     */
    public $handle = null;

    public function options($actionID): array
    {
        $options = parent::options($actionID);
        $options[] = 'handle';

        return $options;
    }

    /**
     *  Export a single Reporter report using --handle=myReportHandle
     */
    public function actionIndex(): int
    {
        // Error if the user did not supply a --handle param
        if (! $this->handle) {
            $this->stderr('You must supply a --handle parameter to indicate which report to process:'.PHP_EOL, Console::FG_RED);
            $this->stderr('php craft reporter/report --handle=myReportHandle'.PHP_EOL);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        // Get the report by its handle
        $report = Reporter::getInstance()->getReport($this->handle);

        // Error out if we did not find a match
        if (! $report) {
            $this->stderr('You must specify the handle of an existing report to export.'.PHP_EOL, Console::FG_RED);
            $this->stderr('Consult `config/reporter.php` to ensure you created at least one report and properly referenced the handle.'.PHP_EOL, Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        // Process the closure and reassign the output for the remaining steps
        $report = $report();

        // Provide a bit of visual feedback to the user that their report is being processed
        $this->stdout('Added an export of the "'.$report['name'].'" report to the queue.'.PHP_EOL, Console::FG_BLUE);

        // Send the process to the queue
        Craft::$app->getQueue()->push(
            new ExportJob([
                'handle' => $this->handle,
                'name' => $report['name'],
            ])
        );

        return ExitCode::OK;
    }
}
