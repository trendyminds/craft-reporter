<?php

namespace trendyminds\reporter\controllers;

use Craft;
use craft\elements\Asset;
use craft\web\Controller;
use trendyminds\reporter\jobs\ExportJob;
use trendyminds\reporter\Reporter;

class DefaultController extends Controller
{
    protected $allowAnonymous = false;

    /**
     * The landing page controller that displays all available reports that can be exported.
     */
    public function actionIndex()
    {
        $this->requirePermission('accessPlugin-reporter');

        return $this->renderTemplate('reporter/index', [
            'displayName' => Reporter::getInstance()->getSettings()->displayName,
            'reports' => collect(Reporter::getInstance()->getSettings()->reports)
                ->map(fn ($i) => $i())
                ->filter(fn ($i, $key) => $key)
                ->map(fn ($i, $key) => [
                    'name' => $i['name'],
                    'handle' => $key,
                    'description' => $i['description'] ?? '',
                ])
                ->values()
                ->toArray(),
        ]);
    }

    /**
     * The list of all exported reports that can be downloaded.
     */
    public function actionExports()
    {
        $this->requirePermission('accessPlugin-reporter');

        $folderVolume = Reporter::getInstance()->getExportPath();

        return $this->renderTemplate('reporter/exports', [
            'reports' => Asset::find()->folderId($folderVolume->id)->filename('*.csv')->all(),
        ]);
    }

    /**
     * The "run" action that triggers a new job to export a given report.
     */
    public function actionRun()
    {
        $this->requirePermission('accessPlugin-reporter');
        $this->requirePostRequest();

        $reportHandle = $this->request->getRequiredBodyParam('report');
        $report = Reporter::getInstance()->getReport($reportHandle)();

        Craft::$app->getQueue()->push(
            new ExportJob([
                'handle' => $reportHandle,
                'name' => $report['name'],
            ])
        );

        $this->setSuccessFlash("Running \"{$report['name']}\" report");

        return $this->redirectToPostedUrl();
    }
}
