<?php

namespace shirase\grid\sortable;

use yii\web\AssetBundle;

class SerialColumnAsset extends AssetBundle {

    public $js = ['js/grid-sortable.js'];
    public $depends = [
        'yii\jui\JuiAsset',
    ];
    //public $publishOptions = ['forceCopy'=>true];

    public function init() {
        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }
} 