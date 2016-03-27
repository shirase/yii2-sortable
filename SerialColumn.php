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

        /**
         * @var $modelClass ActiveRecord
         */
        $modelClass = $this->grid->dataProvider->query->modelClass;

        if(($model = new $modelClass()) && $model->hasMethod('insertBefore')) {
            $sort = $this->grid->dataProvider->getSort();
            if(!$sort) return;

            if(!$sort->defaultOrder) {
                $sort->defaultOrder = array($this->attribute=>SORT_ASC);
            }

            if(!isset($sort->attributes[$this->attribute])) {
                $sort->attributes[$this->attribute] = [
                    'label'=>'#',
                    'asc'=>$this->attribute.' ASC',
                    'desc'=>$this->attribute.' DESC',
                ];
            } else {
                $sort->attributes[$this->attribute]['label'] = '#';
            }

            if ($orders = $this->grid->dataProvider->getSort()->orders) {
                if (array_keys($orders)[0] !== $this->attribute) {
                    return;
                }
            }

            if(($params = Yii::$app->request->post('SortableSerialColumn')) && $params['id']===$this->grid->id) {
                while(ob_get_level()) ob_end_clean();

                $insert = isset($params['insert']) ? $params['insert'] : null;

                if(!isset($params['model'])) {
                    throw new HttpException(500);
                }

                if(isset($params['page'])) {
                    $this->grid->dataProvider->getPagination()->page = $params['page'] - 1;
                    $models = $this->grid->dataProvider->getModels();
                    if(!$models) {
                        throw new HttpException(500);
                    }

                    $action = 'insertBefore';
                    $insert = $models[0]->primaryKey;
                } else {
                    $action = 'insert'.ucfirst($params['action']);
                    if ($orders[$this->attribute]===SORT_DESC) {
                        if ($action === 'insertBefore') {
                            $action = 'insertAfter';
                        } else {
                            $action = 'insertBefore';
                        }
                    }
                }

                $model = $modelClass::findOne($params['model']);
                $model->$action($insert);

                Yii::$app->end();
            }

            $this->contentOptions = function ($model, $key, $index, $column) {
                return ['data-sortable-serial-column-id'=>$key];
            };

            $view = $this->grid->getView();

            SerialColumnAsset::register($view);

            $options = ['id'=>$this->grid->id, 'url'=>Yii::$app->request->url];
            if ($this->sortOptions !== null) {
                $options = array_merge($options, $this->sortOptions);
            }

            $id = $this->grid->options['id'];
            $view->registerJs("jQuery('#$id').GridView_sortable(".Json::encode($options).");");
        }
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