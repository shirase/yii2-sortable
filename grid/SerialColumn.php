<?php

namespace shirase\sortable\grid;

use yii\db\ActiveRecord;
use yii\helpers\Json;
use Yii;
use yii\web\HttpException;

/**
 * Class SerialColumn
 * @package shirase\sortable\grid
 */
class SerialColumn extends \yii\grid\SerialColumn {

    public $sortOptions = null;

    public function init()
    {
        parent::init();

        /**
         * @var $modelClass ActiveRecord
         */
        $modelClass = $this->grid->dataProvider->query->modelClass;

        if(($model = new $modelClass()) && $model->hasMethod('insertBefore')) {
            if(($params = Yii::$app->request->get('SortableSerialColumn')) && $params['id']===$this->grid->id) {
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
                }

                $model = $modelClass::find($params['model']);
                if(!$model->hasMethod($action)) {
                    throw new HttpException(500);
                }
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
}