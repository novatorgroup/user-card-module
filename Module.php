<?

namespace novatorgroup\usercard;

use novatorgroup\usercard\components\CheckCardEvent;

/**
 * Модуль для получения данных по дисконтным картам
 * и привязки к аккаунту пользователя
 */
class Module extends \yii\base\Module
{
    /**
     * События
     */
    const EVENT_AFTER_CHECK_CARD = 'afterCheckCard';
    const EVENT_ERROR_CHECK_CARD = 'errorCheckCard';

    /**
     * Допустимые префиксы карт
     * @var array
     */
    public $prefixes = [];

    /**
     * Карта найдена
     * @param \novatorgroup\usercard\models\DiscountCardForm $card
     */
    public function afterCheckCard($card)
    {
        $event = new CheckCardEvent;
        $event->card = $card;
        $this->trigger(self::EVENT_AFTER_CHECK_CARD, $event);
    }

    /**
     * Карта не найдена
     * @param \novatorgroup\usercard\models\DiscountCardForm $card
     * @param string $message
     */
    public function errorCheckCard($card, $message)
    {
        $event = new CheckCardEvent;
        $event->card = $card;
        $event->message = $message;
        $this->trigger(self::EVENT_ERROR_CHECK_CARD, $event);
    }
}