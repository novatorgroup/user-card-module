<?php

namespace novatorgroup\usercard\controllers;

use Yii;
use yii\bootstrap\Html;
use yii\web\Controller;
use yii\web\Response;
use novatorgroup\usercard\components\UserCard;
use novatorgroup\usercard\models\DiscountCardForm;

class DefaultController extends Controller
{
    /**
     * Привязка дисконтной карты
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new DiscountCardForm();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            if (!$model->validate()) {
                return [
                    'result' => false,
                    'message' => Html::errorSummary($model, ['header' => false, 'class' => 'alert alert-danger'])
                ];
            }

            $userCard = new UserCard();
            $userCard->card = $model->fullName;
            $result = $userCard->getInfo();

            /** @var \novatorgroup\usercard\Module $module */
            $module = Yii::$app->getModule('card');

            if (isset($result->discount)) {
                $module->afterCheckCard($model);

                return ['result' => true];
            } else {
                $module->errorCheckCard($model, $result->error);

                return [
                    'result' => false,
                    'message' => Html::tag('div', $result->error, ['class' => 'alert alert-danger'])
                ];
            }
        }

        return ['result' => false, 'message' => 'Ошибка привязки карты'];
    }
}
