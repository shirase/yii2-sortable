<?php

namespace shirase\grid\sortable;

use yii\base\Widget;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\HttpException;

class Sortable extends Widget
{
    public $attribute = 'pos';

    public $sortOptions = null;

    public $sortItemsSelector;

    /**
     * @var ActiveDataProvider
     */
    public $dataProvider;

    public $containerSelector;

    public function init()
    {
        if (!$this->dataProvider) {
            throw new HttpException(500, 'dataProvider is required');
        }

        /**
         * @var $model ActiveRecord
         * @var $modelClass ActiveRecord
         */
        $modelClass = $this->dataProvider->query->modelClass;
        if(($model = new $modelClass()) && $model->hasMethod('insertBefore')) {
            $sort = $this->dataProvider->getSort();
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

            if ($orders = $this->dataProvider->getSort()->orders) {
                if (array_keys($orders)[0] !== $this->attribute) {
                    return;
                }
            }

            if(($params = \Yii::$app->request->post('Sortable')) && $params['id']===$this->id) {
                while(ob_get_level()) ob_end_clean();

                $insert = isset($params['insert']) ? $params['insert'] : null;

                if(!isset($params['model'])) {
                    throw new HttpException(500);
                }

                if(isset($params['page'])) {
                    $this->dataProvider->getPagination()->page = $params['page'] - 1;
                    $models = $this->dataProvider->getModels();
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

                \Yii::$app->end();
            }

            $view = $this->getView();

            SortableAsset::register($view);

            $options = ['id'=>$this->id, 'url'=>\Yii::$app->request->url, 'items'=>$this->sortItemsSelector];
            if ($this->sortOptions !== null) {
                $options = array_merge($options, $this->sortOptions);
            }

            $selector = $this->containerSelector ? $this->containerSelector : '#'.$this->id;
            $view->registerJs("jQuery('{$selector}').sortableItems(".Json::encode($options).");");

            if (!$this->containerSelector) {
                ob_start();
            }
        }
    }

    public function run() {
        if (!$this->containerSelector) {
            echo Html::tag('div', ob_get_clean(), ['id' => $this->id]);
        }
    }
}