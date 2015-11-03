<?php
namespace novatorgroup\usercard\components;

use Yii;
use yii\base\Object;
use novatorgroup\nss_connect\NssDirect;

/**
 * Данные по дисконтной карте
 *
 * @property string $card
 * @property float $summ
 */
class UserCard extends Object
{
    /**
     * @var string Номер карты
     */
    private $card = '';

    /**
     * @var float Сумма текущей покупки
     */
    private $summ = 0;

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
     * Сумма текущей покупки
     * @param float $value
     */
    public function setSumm($value)
    {
        $this->summ = str_replace('.', ',', $value);
    }

    /**
     * Полное имя карты
     */
    public static function getCard()
    {
        return Yii::$app->session->get('card');
    }

    /**
     * Скидка
     * @return string
     */
    public static function discountName()
    {
        $discount = Yii::$app->session->get('discount');
        return $discount === null ? 'неизвестна' : $discount.'%';
    }

    /**
     * Скидка
     * @return float
     */
    public static function discount()
    {
        $discount = Yii::$app->session->get('discount');
        return $discount === null ? 1 : 1 - ($discount / 100);
    }

    /**
     * Сброс карты
     */
    public static function reset()
    {
        Yii::$app->session->remove('card');
        Yii::$app->session->remove('discount');
    }

    /**
     * Запрос на проверку карты
     * @param $card string
     * @return \SimpleXMLElement | \StdClass
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
            'card' => $this->card,
            'summ' => $this->summ
        ]);

        if (isset($result->discount)) {
            Yii::$app->session->set('card', $this->card);
            if ($this->summ == 0) {
                Yii::$app->session->set('discount', (string)$result->discount);
            }
        }

        return $result;
    }
}