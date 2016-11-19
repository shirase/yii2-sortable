<?php

namespace shirase\grid\sortable;

use yii\web\AssetBundle;

class SortableAsset extends AssetBundle {

    public $js = ['js/sortable-items.js'];
    public $depends = [
        'yii\jui\JuiAsset',
    ];
    //public $publishOptions = ['forceCopy'=>true];

    public function init() {
        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }
} 