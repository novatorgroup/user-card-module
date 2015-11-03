<?php

namespace novatorgroup\usercard\controllers;

use Yii;
use novatorgroup\usercard\components\UserCard;
use novatorgroup\usercard\models\DiscountCardForm;
use yii\bootstrap\Html;
use yii\web\Controller;
use yii\web\Response;

/**
 * Дисконтная карта
 */
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
            $userCard->card = $model->cardFullName(true);
            $result = $userCard->getInfo();

            if (isset($result->discount))
            {
                //Yii::app()->cart->clearCardInfo(); // TODO

                $this->sendEmailManager('Дисконтная карта успешно привязана: '.$model->cardFullName(true));
                return ['result' => true];
            }
            else
            {
                $this->sendEmailManager('Ошибка привязки карты: ('.$model->cardFullName(true).'): '.$result->error);

                return [
                    'result' => false,
                    'message' => Html::tag('div', $result->error, ['class' => 'alert alert-danger'])
                ];
            }
        }

        return ['result' => false, 'message' => 'Ошибка привязки карты'];
    }

    /**
     * Уведовление на почту оператору
     * @param string $message
     */
    private function sendEmailManager($message)
    {
        // TODO

/*        $objEmail = new CEmail();
        $objEmail->compose('admin', ['message' => $message])
                 ->setSubject('Сообщение с сайта '.Yii::app()->name)
                 ->send(Yii::app()->params['cardEmail']);*/
    }
}
