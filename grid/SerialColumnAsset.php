<?php

namespace shirase\sortable\grid;

use yii\web\AssetBundle;

class SerialColumnAsset extends AssetBundle {

    public $sourcePath = '@shirase/sortable/assets';
    public $js = ['js/grid-sortable.js'];
    public $depends = [
        'yii\jui\JuiAsset',
    ];
    //public $publishOptions = ['forceCopy'=>true];
} 