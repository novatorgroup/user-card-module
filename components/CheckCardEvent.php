<?php

namespace novatorgroup\usercard\components;

use yii\base\Event;

class CheckCardEvent extends Event
{
    /**
     * @var \novatorgroup\usercard\models\DiscountCardForm
     */
    public $card;

    /**
     * @var string
     */
    public $message;

    /**
     * @var array дополнительные параметры
     */
    public $params = [];
}