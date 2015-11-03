<?

namespace novatorgroup\usercard\widgets;

use yii\base\Widget;

class DiscountCardWidget extends Widget
{
    public $success = 'location.reload();';

    public function run()
    {
        return $this->render('form', ['success' => $this->success]);
    }
}

