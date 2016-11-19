<?php

namespace shirase\grid\sortable;

use yii\db\ActiveRecord;
use yii\helpers\Json;
use Yii;
use yii\web\HttpException;

/**
 * Class SerialColumn
 * @package shirase\grid\sortable
 */
class SerialColumn extends \kartik\grid\SerialColumn {

    public $sortOptions = null;
    public $sortLinkOptions = [];

    public $attribute = 'pos';

    public function init()
    {
        parent::init();

        Sortable::widget(['attribute'=>$this->attribute, 'sortOptions'=>$this->sortOptions, 'sortItemsSelector'=>'table tr']);

        $this->contentOptions = function ($model, $key, $index, $column) {
            return ['data-sortable-id'=>$key];
        };
    }

    protected function renderHeaderCellContent()
    {
        $provider = $this->grid->dataProvider;
        if (($sort = $provider->getSort()) !== false && $sort->hasAttribute($this->attribute)) {
            /*if ($orders = $sort->orders) {
                if (array_keys($orders)[0] === $this->attribute) {
                    $this->sortLinkOptions = array_merge($this->sortLinkOptions, ['style'=>'color:red']);
                }
            }*/

            $this->sortLinkOptions = array_merge($this->sortLinkOptions, ['data-pjax'=>'0']);

            return $sort->link($this->attribute, $this->sortLinkOptions);
        } else {
            return parent::renderHeaderCellContent();
        }
    }
}