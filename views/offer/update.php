<?php
/* @var $this yii\web\View */
/* @var $model app\models\Offer */

echo $this->render('create', ['model' => $model]);

// Добавляем JavaScript для обработки формы
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
