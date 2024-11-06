<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Офферы';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="offer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::button('Создать оффер', [
            'value' => \yii\helpers\Url::to(['offer/create']),
            'class' => 'btn btn-success',
            'id' => 'createOfferButton',
        ]) ?>
    </p>

    <!-- Фильтрация -->
    <div class="row">
        <div class="col-md-4">
            <?= Html::textInput('offer_name', Yii::$app->request->get('offer_name'), [
                'class' => 'form-control',
                'placeholder' => 'Название оффера',
                'id' => 'offerNameFilter',
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= Html::textInput('representative_email', Yii::$app->request->get('representative_email'), [
                'class' => 'form-control',
                'placeholder' => 'Email представителя',
                'id' => 'representativeEmailFilter',
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= Html::button('Фильтровать', [
                'class' => 'btn btn-primary',
                'id' => 'filterButton',
            ]) ?>
            <?= Html::a('Сбросить', ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <br>

    <?php Pjax::begin(['id' => 'offersPjax']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'label' => 'ID',
                'contentOptions' => ['class' => 'sortable', 'data-attribute' => 'id'],
                'headerOptions' => ['style' => 'cursor:pointer;'],
            ],
            [
                'attribute' => 'offer_name',
                'label' => 'Название оффера',
                'contentOptions' => ['class' => 'sortable', 'data-attribute' => 'offer_name'],
                'headerOptions' => ['style' => 'cursor:pointer;'],
            ],
            'representative_email:email',
            'representative_phone',
            [
                'attribute' => 'date_added',
                'label' => 'Дата добавления',
                'format' => ['date', 'php:Y-m-d H:i:s'],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::button('Редактировать', [
                            'value' => \yii\helpers\Url::to(['offer/update', 'id' => $model->id]),
                            'class' => 'btn btn-primary btn-sm updateOfferButton',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::button('Удалить', [
                            'class' => 'btn btn-danger btn-sm deleteOfferButton',
                            'data-id' => $model->id,
                            'data-url' => \yii\helpers\Url::to(['offer/delete', 'id' => $model->id]),
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<!-- Модальное окно -->
<?php
use yii\bootstrap5\Modal;
Modal::begin([
    'id' => 'modal',
    'title' => 'Модальное окно',
    'size' => 'modal-lg',
]);
echo "<div id='modalContent'></div>";
Modal::end();
?>

<?php
$script = <<< JS
// Функция для обновления параметров URL
function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "i");
    var hash;

    if (re.test(uri)) {
        if (typeof value !== 'undefined' && value !== null)
            return uri.replace(re, '$1' + key + "=" + value + '$2$3');
        else {
            hash = uri.split('#');
            uri = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
            if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                uri += '#' + hash[1];
            return uri;
        }
    }
    else {
        if (typeof value !== 'undefined' && value !== null) {
            var separator = uri.indexOf('?') !== -1 ? '&' : '?';
            hash = uri.split('#');
            uri = hash[0] + separator + key + '=' + value;
            if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                uri += '#' + hash[1];
            return uri;
        }
        else
            return uri;
    }
}

// Создание оффера
$('#createOfferButton').on('click', function() {
    $.get($(this).attr('value'), function(data) {
        $('#modal').modal('show').find('#modalContent').html(data);
    });
});

// Редактирование оффера
$(document).on('click', '.updateOfferButton', function() {
    var url = $(this).attr('value');
    $.get(url, function(data) {
        $('#modal').modal('show').find('#modalContent').html(data);
    });
});

// Удаление оффера
$(document).on('click', '.deleteOfferButton', function() {
    if (confirm('Вы уверены, что хотите удалить этот оффер?')) {
        var url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'POST',
            success: function(data) {
                if (data.status === 'success') {
                    $.pjax.reload({container:'#offersPjax'});
                    alert('Оффер успешно удален.');
                } else {
                    alert('Ошибка при удалении оффера.');
                }
            },
            error: function() {
                alert('Ошибка при удалении оффера.');
            }
        });
    }
});

// Фильтрация
$('#filterButton').on('click', function() {
    var offerName = $('#offerNameFilter').val();
    var email = $('#representativeEmailFilter').val();
    var currentUrl = window.location.href;
    var newUrl = updateQueryStringParameter(currentUrl, 'offer_name', offerName);
    newUrl = updateQueryStringParameter(newUrl, 'representative_email', email);
    $.pjax.reload({container:'#offersPjax', url: newUrl});
});

// Сортировка
$(document).on('click', '.sortable', function() {
    var attribute = $(this).data('attribute');
    var currentSort = getUrlParameter('sort');
    var newSort = attribute;
    if (currentSort === attribute) {
        newSort = '-' + attribute; // Обратная сортировка
    }
    var currentUrl = window.location.href;
    var newUrl = updateQueryStringParameter(currentUrl, 'sort', newSort);
    $.pjax.reload({container:'#offersPjax', url: newUrl});
});

// Получение параметра из URL
function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
  
    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
  
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

JS;
$this->registerJs($script);
?>
