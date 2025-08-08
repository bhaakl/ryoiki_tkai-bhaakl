<?php

use app\helpers\NumberHelper;
use app\models\Product;
use app\models\ProductionStage;
use app\widgets\Card;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model app\models\search\OrderSearch */
/* @var $data array */

$this->title = 'Печать задания на производство ' . ($data['type'] == Product::SHEET_TYPE_INTERNAL ? 'внутренней СП' : ' внешней СП');
$firstSpecPositions = $data['positions'][0] ?? [];
?>

<?php Card::begin([]); ?>

<style>
    table {
        width: 100%;
        border-collapse: collapse;
        height: 100vh;
    }
    td {
        padding: 10px;
        vertical-align: top;
    }
    .left-column {
        width: 40%;
    }
    .right-column {
        width: 60%;
        background-color: #eeeeee;
        padding: 20px;
    }
    iframe {
        width: 100%;
        height: 100%;
        border: none;
        background-color: white;
    }
</style>

<table>
    <tr>
        <td class="left-column">
            <p>
                <?php
                    if ($data['type'] == Product::SHEET_TYPE_INTERNAL) {
                        echo Html::a(
                            '<i class="fa fa-print"></i> Бланк для внешней СП',
                            Url::to(['production-tickets/preview', 'id' => $model->id, 'stage' => ProductionStage::STAGE_SHEET, 'type' => Product::SHEET_TYPE_EXTERNAL]),
                            ['title' => 'Бланк для внешней СП']);
                    } else {
                        echo Html::a(
                            '<i class="fa fa-print"></i> Бланк для внутренней СП',
                            Url::to(['production-tickets/preview', 'id' => $model->id, 'stage' => ProductionStage::STAGE_SHEET, 'type' => Product::SHEET_TYPE_INTERNAL]),
                            ['title' => 'Бланк для внутренней СП']);
                    }

                ?>
            </p>
            <form id="preview-form">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="control-label">Спецификация</label>
                            <?= Html::input('text', 'specification', 'Спецификация 1 из 1', ['class' => 'form-control', 'maxlength' => 30]) ?>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="control-label">Направление</label>
                            <?= Html::input('text', 'direction', 'М1', ['class' => 'form-control']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="control-label">Покрытие</label>
                            <?= Html::input('text', 'coating', 'Полиэстер', ['class' => 'form-control', 'maxlength' => 30]) ?>
                        </div>
                    </div>
                </div>
                <?php if ($data['type'] === Product::SHEET_TYPE_INTERNAL) : ?>
                <div class="card">
                    <div class="card-header">Позиции</div>
                    <div class="card-body" id="itemsContainer">
                        <div class="row">
                            <div class="col-xs-12 col-sm-3 col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Длина, мм</label>
                                    <?= MaskedInput::widget([
                                        'name' => 'length[]',
                                        'value' => $firstSpecPositions[0]['length'] ?? 0,
                                        'clientOptions' => NumberHelper::LENGTH_INPUT_CONFIG,
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-3 col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Количество, шт</label>
                                    <?= MaskedInput::widget([
                                        'name' => 'quantity[]',
                                        'value' => $firstSpecPositions[0]['quantity'] ?? 1,
                                        'clientOptions' => NumberHelper::LENGTH_INPUT_CONFIG,
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a class="btn btn-success btn-sm actionButton" href="#" title="Добавить строку" id="addItemButton"><span class="actionIcon fa fa-plus"></span></a>
                    </div>
                </div>
                <?php endif; ?>
            </form>
            <?php if ($data['type'] === Product::SHEET_TYPE_INTERNAL) : ?>
            <div class="text-center">
                <?= Html::submitButton('<i class="fa fa-print"></i> Печать', ['class' => 'btn btn-primary print-button', 'title' => 'Печать']) ?>
                <?= Html::submitButton('<i class="fa fa-sync"></i> Сохранить и обновить', ['class' => 'btn btn-info refresh-button', 'title' => 'Сохранить и обновить']) ?>
            </div>
            <?php else : ?>
            <div class="text-center mt-2">
                <?= Html::submitButton('<i class="fa fa-print"></i> Печать', ['class' => 'btn btn-primary print-button', 'title' => 'Печать']) ?>
                <?= Html::submitButton('<i class="fa fa-sync"></i> Обновить', ['class' => 'btn btn-info refresh-button', 'title' => 'Обновить']) ?>
            </div>
            <?php endif; ?>
        </td>
        <td class="right-column">
            <iframe src="<?= Url::to([
                'production-tickets/print',
                'id' => $model->id,
                'stage' => ProductionStage::STAGE_SHEET,
                'type' => $data['type'],
            ]) ?>" id="preview"></iframe>
        </td>
    </tr>
</table>

<?php

Card::end();

$js = <<< JS
function getUrlWithParameters(iframe) {
    const iframeSrc = iframe.src;
    
    const preview_params = new URLSearchParams(window.location.search);
    const stage = preview_params.get('stage');
    const type = preview_params.get('type');
    
    const url = new URL(iframeSrc);
    url.search = $('#preview-form').serialize();
    
    const params = new URLSearchParams(url.search);
    params.set('id', preview_params.get('id'));
    params.set('stage', stage);
    params.set('type', type);
    url.search = params.toString();
    
    return url.toString();
}
$(".refresh-button").on("click", function(e) {
    const iframe = document.getElementById('preview');
    iframe.src = getUrlWithParameters(iframe);
});
$(".print-button").on("click", function(e) {
    const iframe = document.getElementById('preview');
    window.open(getUrlWithParameters(iframe) + '&print=true', "_blank");
});

const itemsContainer = document.getElementById('itemsContainer');
const addItemButton = document.getElementById('addItemButton');

function createItem(length = 0, quantity = 1) {
    const newRow = document.createElement('div');
    newRow.classList.add('row');
    
    ['length[]', 'quantity[]'].forEach((item) => {
        let newCol = document.createElement('div');
        newCol.classList.add('col-xs-12');
        newCol.classList.add('col-sm-3');
        newCol.classList.add('col-md-3');

        const group = document.createElement('div');
        group.classList.add('form-group');
        
        const input = document.createElement('input');
        input.type = 'text';
        input.name = item;
        input.classList.add('form-control');
        input.value = item === 'length[]' ? length : quantity;
        
        const inputmaskConfig = $("input[name='" + item + "']").first().attr('data-plugin-inputmask');
        $(input).inputmask(window[inputmaskConfig]);
        
        group.appendChild(input);
        newCol.appendChild(group);
        newRow.appendChild(newCol);
    });

    let newCol = document.createElement('div');
    newCol.classList.add('col-xs-12');
    newCol.classList.add('col-sm-3');
    newCol.classList.add('col-md-3');

    const removeButton = document.createElement('a');
    removeButton.innerHTML = '<span class="fa fa-trash" title="Удалить строку"></span>';
    removeButton.href = '#';
    removeButton.classList.add('btn');
    removeButton.classList.add('btn-secondary');
    removeButton.classList.add('btn-sm');
    removeButton.classList.add('remove-btn');
    removeButton.addEventListener('click', function (e) {
        e.preventDefault();
        itemsContainer.removeChild(newRow);
    });
    
    newCol.appendChild(removeButton);
    newRow.appendChild(newCol);

    return newRow;
}

addItemButton.addEventListener('click', function (e) {
    e.preventDefault();
    const newItem = createItem();
    itemsContainer.appendChild(newItem);
});
JS;

if (!empty($firstSpecPositions) && count($firstSpecPositions) > 1) {
    for ($i = 1, $iMax = count($firstSpecPositions); $i < $iMax; $i++) {
        $js .= <<< JS
var newItem = createItem({$firstSpecPositions[$i]['length']}, {$firstSpecPositions[$i]['quantity']});
itemsContainer.appendChild(newItem);
JS;
    }
}

$this->registerJs($js);
?>