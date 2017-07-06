<?php

namespace novatorgroup\usercard\models;

use Yii;
use yii\base\Model;

/**
 * Форма привязки дисконтной карты
 *
 * @property string $fullName
 */
class DiscountCardForm extends Model
{
    public $prefix;
    public $series;
    public $number;
    public $name1;
    public $name2;
    public $name3;

    public function rules()
    {
        return [
            [['name1', 'name2', 'name3', 'series', 'number'], 'trim'],
            [['name1', 'name2', 'name3'], 'processNamePart'],
            [['prefix', 'series', 'number', 'name1', 'name2', 'name3'], 'required'],
            [['series'], 'string', 'length' => 4],
            [['number'], 'string', 'length' => 3],
            [['name1', 'name2', 'name3'], 'string', 'max' => 128]
        ];
    }

    /**
     * Подготовка части Ф.И.О.
     * @param $attribute
     */
    public function processNamePart($attribute)
    {
        $this->$attribute = preg_replace('/s+/', '', $this->$attribute);

        mb_internal_encoding('UTF-8');
        $result = [];
        foreach (explode('-', $this->$attribute) as $part) {
            $result[] = $this->upperFirst($part);
        }

        $this->$attribute = implode('-', $result);
    }

    private function upperFirst($value)
    {
        return mb_strtoupper(mb_substr($value, 0, 1)) . mb_strtolower(mb_substr($value, 1));
    }

    public function attributeLabels()
    {
        return [
            'prefix' => 'Префикс дисконтной карты',
            'series' => 'Серия дисконтной карты',
            'number' => 'Номер дисконтной карты',
            'name1' => 'Фамилия',
            'name2' => 'Имя',
            'name3' => 'Отчество',
        ];
    }

    public static function getPrefixes()
    {
        /** @var \novatorgroup\usercard\Module $module */
        /** @noinspection OneTimeUseVariablesInspection */
        $module = Yii::$app->getModule('card');
        return $module;
    }

    /**
     * Наименование карты
     * @param bool $withName
     * @return string
     */
    public function getFullName($withName = true)
    {
        $card = $this->prefix . ' ' . $this->series . ' ' . $this->number;
        if ($withName) {
            $card .= ' ' . $this->name1 . ' ' . $this->name2 . ' ' . $this->name3;
        }

        return $card;
    }
}