<?php

namespace novatorgroup\usercard\components;

use novatorgroup\service1c\HttpService;
use Yii;
use yii\base\BaseObject;

/**
 * Данные по дисконтной карте
 *
 * @property string $card
 * @property string $type
 * @property float $money
 */
class UserCard extends BaseObject
{
    /**
     * @var string Номер карты
     */
    private $card = '';

    public $name1 = '';
    public $name2 = '';
    public $name3 = '';

    private $_error;

    /**
     * Полное имя карты
     * @param $value
     */
    public function setCard($value)
    {
        $this->card = $value;

        if (preg_match('/.* \d{4} \d{3} (.*) (.*) (.*)/u', $value, $mathes)) {
            $this->name1 = $mathes[1];
            $this->name2 = $mathes[2];
            $this->name3 = $mathes[3];
        }
    }

    /**
     * Полное имя карты
     */
    public static function getCard()
    {
        return Yii::$app->session->get('card');
    }

    /**
     * Вид дисконтной карты
     */
    public static function getType()
    {
        return Yii::$app->session->get('card-type');
    }

    /**
     * Сумма накоплений по карте
     */
    public static function getMoney()
    {
        return Yii::$app->session->get('card-money');
    }

    public function getError()
    {
        return $this->_error;
    }

    /**
     * Сброс карты
     */
    public static function reset()
    {
        Yii::$app->session->remove('card');
        Yii::$app->session->remove('card-type');
        Yii::$app->session->remove('card-money');
    }

    /**
     * Запрос на проверку карты
     * @param $card string
     * @return \novatorgroup\usercard\components\Infodk
     */
    public function getInfo($card = null)
    {
        if ($card !== null) {
            $this->setCard($card);
        }

        /** @var \novatorgroup\usercard\components\Infodk $result */
        $result = null;

        /** @var \novatorgroup\usercard\Module $module */
        $module = Yii::$app->getModule('card');
        if ($module) {
            $httpService = new HttpService($module->params['serviceConfig']);
            $response = $httpService->get('infodk', [$this->card]);
            if ($response->error) {
                $this->_error = 'Сервис временно недоступен.';
            } else {
                if ($response->code == 200) {
                    $result = @simplexml_load_string($response->result, Infodk::class);
                    Yii::$app->session->set('card', (string)$result->Name);
                    Yii::$app->session->set('card-type', (string)$result->Vid);
                    Yii::$app->session->set('card-money', (float)str_replace(',', '.', $result->Summa));
                } elseif ($response->code == 404) {
                    $this->_error = 'Карта не найдена.';
                } else {
                    $this->_error = 'Сервис временно недоступен.';
                }
            }
        }

        return $result;
    }
}