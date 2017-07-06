<?php
/**
 * @var $this \yii\web\View
 * @var string $success
 * @var string $cancel
 * @var array $params
 */

use novatorgroup\usercard\models\DiscountCardForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$model = new DiscountCardForm();
$url = Url::to(['/card/default/index']);

$js =<<< JS
    var dcResult = false;
    var wnd = $('#discount-card-window');

    $('#card-link').click(function () {
        var btn = $(this);
        $(".discount-card-errors").html("<p class=\'alert alert-info\'>Проверка данных...</p>");
        btn.attr("disabled", "disabled");
        $.post('$url', $('#discount-card-form').serialize(), function (response) {
            btn.removeAttr("disabled");
            if (response.result) {
                dcResult = true;
                wnd.modal('hide');
                $(".discount-card-errors").empty();
                $success
            } else {
                $(".discount-card-errors").html(response.message);
            }
        });
        return false;
    });

    wnd.on('hidden.bs.modal', function () {
        if (!dcResult) { $cancel }
    });
JS;

$css =<<< CSS
label.required:after {
  content: '*';
  margin-left: 5px;
  color: #f00;
}
.card-form-control {
    width: 30%;
    margin-right: 2%;
    display: inline-block;
}
CSS;

$this->registerJs($js, \yii\web\View::POS_READY);
$this->registerCss($css);

?>
<div class="modal fade" id="discount-card-window">
    <div class="modal-dialog">

        <?php
        $form = ActiveForm::begin([
            'action' => '/card/default/index',
            'id' => 'discount-card-form',
            'enableAjaxValidation' => false,
            'layout' => 'horizontal'
        ]);

        echo Html::hiddenInput('params', json_encode($params));
        ?>

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h4 class="modal-title">Данные дисконтной карты</h4>
            </div>
            <div class="modal-body">
                <div class="discount-card-errors">
                    <div class="alert alert-success">Укажите номер карты и Ф.И.О. владельца.</div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label required">Карта</label>

                    <div class="col-sm-9">
                        <?php echo Html::activeDropDownList($model, 'prefix', DiscountCardForm::getPrefixes(), ['class' => 'form-control card-form-control']); ?>
                        <?php echo Html::activeTextInput($model, 'series', ['required' => 'on', 'class' => 'form-control card-form-control', 'maxlength' => 4, 'placeholder' => '0000', 'spellcheck' => 'false']); ?>
                        <?php echo Html::activeTextInput($model, 'number', ['required' => 'on', 'class' => 'form-control card-form-control', 'maxlength' => 3, 'placeholder' => '000', 'spellcheck' => 'false']); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo Html::activeLabel($model, 'name1', ['class' => 'col-sm-3 control-label required']); ?>
                    <div class="col-sm-9">
                        <?php echo Html::activeTextInput($model, 'name1', ['required' => 'on', 'class' => 'form-control', 'spellcheck' => 'false']); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo Html::activeLabel($model, 'name2', ['class' => 'col-sm-3 control-label required']); ?>
                    <div class="col-sm-9">
                        <?php echo Html::activeTextInput($model, 'name2', ['required' => 'on', 'class' => 'form-control', 'spellcheck' => 'false']); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo Html::activeLabel($model, 'name3', ['class' => 'col-sm-3 control-label required']); ?>
                    <div class="col-sm-9">
                        <?php echo Html::activeTextInput($model, 'name3', ['required' => 'on', 'class' => 'form-control', 'spellcheck' => 'false']); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php echo Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'id' => 'card-link']); ?>
                <a href="#" class="btn btn-default" data-dismiss="modal">Закрыть</a>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>