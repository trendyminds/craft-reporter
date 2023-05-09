<?php

namespace trendyminds\reporter\assetbundles;

use craft\web\AssetBundle;

class ReporterBundle extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@trendyminds/reporter/assetbundles/resources';
        $this->css = ['reporter.css'];

        parent::init();
    }
}
