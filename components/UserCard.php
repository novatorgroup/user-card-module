<?php

namespace novatorgroup\usercard\components;

use novatorgroup\service1c\HttpService;
use novatorgroup\usercard\Module;
use Yii;
use yii\base\BaseObject;

/**
 * Данные по дисконтной карте
 *
 * @property string $card
 * @property string $type
 * @property string $error
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
     * @var string
     */
    private $_error;

    /**
     * Полное имя карты
     * @param $value
     */
    public function setCard($value)
    {
        $this->card = $value;

        if ($names = self::extractClientName($value)) {
            $this->name1 = $names[0];
            $this->name2 = $names[1];
            $this->name3 = $names[2];
        }
    }

    /**
     * Получть ФИО клиента из полного наимнования карты
     * @param string $value
     * @return array
     */
    public static function extractClientName(string $value): array
    {
        if (preg_match('/.* \d{4} \d{3} (.*) (.*) (.*)/u', $value, $mathes)) {
            return [$mathes[1], $mathes[2], $mathes[3]];
        }
        return null;
    }

    /**
     * Полное имя карты
     */
    public static function getCard(): ?string
    {
        return Yii::$app->session->get('card');
    }

    /**
     * Сумма накоплений по карте
     */
    public static function getMoney(): float
    {
        return Yii::$app->session->get('card-money', 0);
    }

    /**
     * Сообщение об ошибке
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->_error;
    }

    /**
     * Сброс карты
     */
    public static function reset(): void
    {
        Yii::$app->session->remove('card');
        Yii::$app->session->remove('card-money');
    }

    /**
     * Запрос на проверку карты
     * @param string|null $card
     * @return ClientCard|null
     */
    public function getInfo(?string $card = null): ?ClientCard
    {
        if ($card) {
            $this->setCard($card);
        }

        if ($result = $this->request('infodk', $this->card)) {
            $this->saveCardInfo($result->getFullCardName(), $result->getMoney());
        }

        return $result;
    }

    /**
     * Поиск карты по номеру телефона
     * @param string $phone (10 цифр)
     * @return ClientCard|null
     */
    public function findByPhone(string $phone): ?ClientCard
    {
        if ($result = $this->request('phone_check', $phone)) {
            $this->saveCardInfo($result->getFullCardName(), $result->getMoney());
        }
        return $result;
    }

    /**
     * Выполнение запроса к 1С
     * @param string $command
     * @param string $param
     * @return ClientCard|null
     */
    private function request(string $command, string $param): ?ClientCard
    {
        if ($httpService = $this->httpService()) {
            $response = $httpService->get($command, [$param]);
            if ($response->error) {
                $this->_error = 'Сервис временно недоступен.';
            } else if ($response->code == 200) {
                /** @var ClientCard $result */
                $result = @simplexml_load_string($response->result, ClientCard::class);
                return $result;
            } elseif ($response->code == 404) {
                $this->_error = 'Карта не найдена.';
            } else {
                $this->_error = 'Сервис временно недоступен.';
            }
        }

        return null;
    }

    /**
     * @return HttpService|null
     */
    private function httpService(): ?HttpService
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('card');
        if ($module) {
            return new HttpService($module->params['serviceConfig']);
        }
        return null;
    }

    /**
     * @param $card
     * @param $money
     */
    private function saveCardInfo(string $card, float $money): void
    {
        Yii::$app->session->set('card', $card);
        Yii::$app->session->set('card-money', $money);
    }
}