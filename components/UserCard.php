<?php

namespace novatorgroup\usercard\components;

use Yii;
use yii\base\BaseObject;
use novatorgroup\nss_connect\NssDirect;

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
     * @return \novatorgroup\nss_connect\NssResponse|\StdClass
     */
    public function getInfo($card = null)
    {
        if ($card !== null) {
            $this->setCard($card);
        }

        $module = Yii::$app->getModule('card');

        $nss = new NssDirect([
            'ip' => $module->params['nssIP'],
            'port' => $module->params['nssPort'],
        ]);

        $result = $nss->request('CheckCard', [
            'card' => $this->card
        ]);

        if (!empty($result->type)) {
            Yii::$app->session->set('card', $this->card);
            Yii::$app->session->set('card-type', (string)$result->type);
            Yii::$app->session->set('card-money', (float)str_replace(',', '.', $result->money));
        }

        return $result;
    }
}