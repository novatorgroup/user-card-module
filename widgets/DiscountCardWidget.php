<?

namespace novatorgroup\usercard\widgets;

use yii\base\Widget;

class DiscountCardWidget extends Widget
{
    /**
     * @var string JS код выполняемый в случае успешной проверки
     */
    public $success = 'location.reload();';

    /**
     * @var string JS код выполняемый в случае закрытия окна
     */
    public $cancel;

    public function run()
    {
        return $this->render('form', [
            'success' => $this->success,
            'cancel' => $this->cancel
        ]);
    }
}

