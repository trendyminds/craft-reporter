<?php

namespace console\controllers;

Use Craft;

use craft\console\Controller;
use jobs\ExportJob;

class ReportController extends Controller
{
  /**
  * @var string|null The type of sync to run
  */
  public $type = null;

  public function options($actionID)
  {
      $options = parent::options($actionID);
      $options[] = 'type';

      return $options;
  }

  public function actionIndex()
  {
      Craft::$app->getQueue()->push(
          new ExportJob([
              "type" => $this->type
          ])
      );
  }
}
