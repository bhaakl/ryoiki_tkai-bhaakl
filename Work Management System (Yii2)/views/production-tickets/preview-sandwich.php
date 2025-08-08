<?php

use app\assets\InputMaskAsset;
use app\helpers\NumberHelper;
use app\models\Product;
use app\models\ProductionStage;
use app\widgets\Card;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\search\OrderSearch */
/* @var $data array */

$this->title = 'Печать задания на производство стеновой сэндвич-панели';
$firstSpecPositions = $data['positions'][0];
$specificationsJson = json_encode($data['positions'] ?? []);
$sheetPositionsJson = json_encode($data['sheet_positions'] ?? []);

InputMaskAsset::register($this);

function getQuantitySum($specifications, $position = 0) {
    $total = 0;

    foreach ($specifications as $specification) {
        $total += $specification[$position]['quantity'] ?? 0;
    }

    return $total;
}
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
                <?= Html::a(
                    '<i class="fa fa-print"></i> Бланк для внутренней СП',
                    Url::to(['production-tickets/preview', 'id' => $model->id, 'stage' => ProductionStage::STAGE_SHEET, 'type' => Product::SHEET_TYPE_INTERNAL]),
                    ['title' => 'Бланк для внутренней СП']);
                ?>
            </p>
            <form id="preview-form">
                <div class="row">
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
                            <label class="control-label">Покрытие ВНТ</label>
                            <?= Html::input('text', 'inner_coating', 'Полиэстер', ['class' => 'form-control', 'maxlength' => 30]) ?>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="control-label">Покрытие ВНШ</label>
                            <?= Html::input('text', 'outer_coating', 'Полиэстер', ['class' => 'form-control', 'maxlength' => 30]) ?>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" id="specifications">
                    <li class="nav-item">
                        <button class="nav-link" id="add-spec-button" disabled>Добавить задание</button>
                    </li>
                </ul>
                <div class="card">
                    <div class="card-header">Позиции</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-3 col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Длина, мм</label>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-3 col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Количество, шт</label>
                                </div>
                            </div>
                        </div>
                        <div id="itemsContainer">

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6">
                    </div>
                </div>
            </form>
            <div class="text-center">
                <?= Html::submitButton('<i class="fa fa-print"></i> Печать', ['class' => 'btn btn-primary print-button', 'title' => 'Печать']) ?>
                <?= Html::submitButton('<i class="fa fa-sync"></i> Сохранить и обновить', ['class' => 'btn btn-info refresh-button', 'title' => 'Сохранить и обновить']) ?>
            </div>
        </td>
        <td class="right-column">
            <iframe src="<?= Url::to([
                'production-tickets/print',
                'id' => $model->id,
                'stage' => ProductionStage::STAGE_SANDWICH,
                'type' => $data['type'],
            ]) ?>" id="preview"></iframe>
        </td>
    </tr>
</table>

<?php

Card::end();

$inputConfig = json_encode(NumberHelper::LENGTH_INPUT_CONFIG);

$js = <<< JS
let specifications = {$specificationsJson};
let sheetPositions = {$sheetPositionsJson};

const inputConfig = {$inputConfig};

function getUrlWithParameters(iframe) {
    const iframeSrc = iframe.src;
    
    const preview_params = new URLSearchParams(window.location.search);
    const stage = preview_params.get('stage');
    const type = preview_params.get('type');
    const currentSpecification = parseInt($('#specifications a.active').attr('data-spec-number'));
    
    const url = new URL(iframeSrc);
    url.search = $('#preview-form').serialize();
    
    const params = new URLSearchParams(url.search);
    params.set('id', preview_params.get('id'));
    params.set('stage', stage);
    params.set('type', type);
    params.set('specification', currentSpecification);
    url.search = params.toString();
    
    return url.toString();
}

function createItem(length = 0, quantity = 1, maxQuantity) {
    const newRow = document.createElement('div');
    newRow.classList.add('row');
    
    ['length[]', 'quantity[]'].forEach((item) => {
        let newCol = document.createElement('div');
        newCol.classList.add('col-xs-12');
        newCol.classList.add('col-sm-3');
        newCol.classList.add('col-md-3');

        let group = document.createElement('div');
        group.classList.add('form-group');
        
        let input = document.createElement('input');
        input.type = 'text';
        input.name = item;
        input.classList.add('form-control');
        
        let inputmaskConfig = {...inputConfig};

        if (item === 'length[]') {
            input.disabled = true;
            input.readOnly = true;
            input.value = length;    
        } else {
            input.value = quantity;    
            if (maxQuantity !== undefined) {
                inputmaskConfig['max'] = maxQuantity;
            }
        }
        $(input).inputmask(inputmaskConfig);
        
        group.appendChild(input);
        newCol.appendChild(group);
        newRow.appendChild(newCol);
    });

    let newCol = document.createElement('div');
    newCol.classList.add('col-xs-12');
    newCol.classList.add('col-sm-3');
    newCol.classList.add('col-md-3');

    newRow.appendChild(newCol);

    return newRow;
}

function createTab(specNumber) {
    const newtab = document.createElement('li');
    newtab.classList.add('nav-item');
    newtab.classList.add('specification-tab');
    
    const link = document.createElement('a');
    link.href = '#';
    link.text = 'Спецификация ' + specNumber;
    link.setAttribute('data-bs-toggle', 'tab');
    link.setAttribute('data-spec-number', specNumber);
    link.classList.add('nav-link');
    link.classList.add('specification-link');
    
    link.addEventListener('click', function (e) {
        e.preventDefault();
        renderSpecificationContent();
        updatePreview();
    });
    
    newtab.appendChild(link);
    
    return newtab;
}

function addSpecification() {
    let newSpecification = [];
    
    for (let i = 0; i < sheetPositions[0].length; i++) {
        let remainder = getQuantitySum(sheetPositions, i) - getQuantitySum(specifications, i);
        newSpecification[i] = {...sheetPositions[0][i]};
        newSpecification[i]['quantity'] = remainder;
    }
        
    specifications.push(newSpecification);        

    renderSpecificationTabs();
    saveSpecifications();
}

function updatePreview() {
    const iframe = document.getElementById('preview');
    iframe.src = getUrlWithParameters(iframe);
}

function saveSpecifications() {
    $.ajax({
        url: '/production-tickets/save-positions?id=' + {$model->id} + '&stage=sandwich',
        data: {
            specifications: specifications
        },
        dataType: 'json',
        method: 'post'
    });
}

function renderSpecificationTabs() {
    const tabsContainer = document.getElementById('specifications');
    let specNumber = tabsContainer.childElementCount;
    while (tabsContainer.childElementCount <= specifications.length) {
        tabsContainer.appendChild(createTab(specNumber++));    
    }
    
    const triggerTabList = document.querySelectorAll('#specifications a');
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl)
    
        triggerEl.addEventListener('click', event => {
            event.preventDefault()
            tabTrigger.show()
        })
    });
}

function renderSpecificationContent() {
    const itemsContainer = document.getElementById('itemsContainer');
    const currentSpecification = parseInt($('#specifications a.active').attr('data-spec-number'));

    itemsContainer.innerHTML = '';
    for (let i = 0; i < specifications[currentSpecification - 1].length; i++) {
        let remainder = getQuantitySum(sheetPositions, i) - getQuantitySum(specifications, i);
        let newItem = createItem(
            specifications[currentSpecification - 1][i]['length'], 
            specifications[currentSpecification - 1][i]['quantity'], 
            specifications[currentSpecification - 1][i]['quantity'] + remainder
        );
        itemsContainer.appendChild(newItem);
    }
}

function renderSpecificationBlock() {
    renderSpecificationTabs();
    bootstrap.Tab.getInstance(document.querySelector('#specifications a')).show();
    renderSpecificationContent();
    updateAddButton();
}

function getQuantitySum(specs, position = 0) {
    let total = 0;

    for (let i = 0; i < specs.length; i++) {
        total += specs[i][position]['quantity'] ?? 0;
    }

    return total;
}

function updateAddButton() {
    let createNewTab = false;
    
    for (let i = 0; i < sheetPositions[0].length; i++) {
        let remainder = getQuantitySum(sheetPositions, i) - getQuantitySum(specifications, i);
        if (remainder > 0) {
            createNewTab = true;
        }
    }
    
    if (createNewTab) {
        $('#add-spec-button').removeAttr('disabled');
    } else {
        $('#add-spec-button').attr('disabled', true);
    }
}

function updateSpecificationsBlock() {
    const quantities = $('#preview-form').find('input[name="quantity[]"]');
    const currentSpecification = parseInt($('#specifications a.active').attr('data-spec-number'));

    for (let i = 0; i < quantities.length; i++) {
        specifications[currentSpecification - 1][i]['quantity'] = parseInt(quantities[i].value);
    }
    
    updateAddButton();
    
    let initSpecLength = specifications.length;
    for (let i = initSpecLength - 1; i >= 0; i--) {
        let isSpecEmpty = true; 
        for (let j = 0; j < specifications[i].length; j++) {
            if (parseInt(specifications[i][j]['quantity']) > 0) {
                isSpecEmpty = false;
            }    
        }
        
        if (isSpecEmpty && specifications.length > 1) {
            specifications.splice(i, 1);
        }
    }
    
    if (specifications.length !== initSpecLength) {
        $('#specifications li.specification-tab').remove();
        renderSpecificationBlock();
    }

    saveSpecifications();
}

$("#add-spec-button").on("click", function(e) {
    e.preventDefault();
    addSpecification();
    $(this).attr('disabled', true);
});

$(".refresh-button").on("click", function(e) {
    updateSpecificationsBlock();
    updatePreview();
});

$(".print-button").on("click", function(e) {
    const iframe = document.getElementById('preview');
    window.open(getUrlWithParameters(iframe) + '&print=true', "_blank");
});

$("a.specification-link").on("click", function(e) {
    e.preventDefault();
    renderSpecificationContent();
    updatePreview();
});

renderSpecificationBlock();
JS;

$this->registerJs($js);
?>