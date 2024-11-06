<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Offer */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="offer-form">
    <?php $form = ActiveForm::begin([
        'id' => 'offer-form',
        'enableAjaxValidation' => true,
        'validationUrl' => \yii\helpers\Url::to(['offer/validate-form']),
    ]); ?>

    <?= $form->field($model, 'offer_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'representative_email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'representative_phone')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$script = <<< JS
$('#offer-form').on('beforeSubmit', function(e) {
    var form = $(this);
    // Отправка данных формы через AJAX
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        success: function (response) {
            if (response === 'success') {
                // Закрыть модальное окно
                $('#modal').modal('hide');
                // Обновить список офферов
                $.pjax.reload({container:'#offersPjax'});
            } else {
                // Показать ошибки
                form.yiiActiveForm('updateMessages', response, true);
            }
        },
        error: function () {
            alert('Ошибка при сохранении оффера.');
        }
    });
    return false; // Предотвращаем обычную отправку формы
});
JS;
$this->registerJs($script);
?>
