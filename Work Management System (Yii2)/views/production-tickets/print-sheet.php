<?php

/* @var $this yii\web\View */
/* @var $model array */
/* @var $data array */

use app\helpers\NumberHelper;
use app\models\Product;

$typeLabel = $data['type'] == Product::SHEET_TYPE_INTERNAL ? 'внутренней' : 'внешней';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Печать задания на производство <?= $typeLabel ?> СП</title>
    <link href="/css/print.css?v=1" rel="stylesheet">
    <script>
        function onPageLoaded() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('print')) {
                window.print();
            }
        }
    </script>
</head>
<body onload="onPageLoaded()">
    <h3>Производственное задание на изготовление <?= $typeLabel ?> сэндвич-панели</h3>
    <table class="top-spacing">
        <tbody>
            <tr class="border-top2">
                <td colspan="2"><?= $data['specification'] ?></td>
                <td>№ заказа</td>
                <td><?= $model['number'] ?></td>
                <td></td>
                <td rowspan="7" class="direction"><?= $data['direction'] ?></td>
            </tr>
            <tr>
                <td colspan="2">Заказчик</td>
                <td colspan="3" class="bold"><?= $model['customerName'] ?></td>
            </tr>
            <tr>
                <td colspan="2">Направление</td>
                <td colspan="3"></td>
            </tr>
            <tr class="border-top2">
                <td colspan="2">Номенклатура</td>
                <td colspan="2" class="bold"><?= $model['productName'] ?></td>
                <td class="bold">в пленке</td>
            </tr>
            <tr class="border-top2">
                <td colspan="2">Утеплитель</td>
                <td colspan="2" class="bold"><?= $data['insulation'] ?></td>
                <td class="bold"><?= $model['insulation_thickness_id'] ?></td>
            </tr>
            <tr class="border-top2">
                <td colspan="2">Внешняя</td>
                <td>Толщина, мм</td>
                <td>Покрытие</td>
                <td>Цвет</td>
            </tr>
            <tr>
                <td colspan="2" class="bold"><?= $data['type'] == Product::SHEET_TYPE_INTERNAL ? $model['inner_type'] : $model['outer_type'] ?></td>
                <td class="bold"><?= $data['type'] == Product::SHEET_TYPE_INTERNAL ? $model['inner_thickness'] : $model['outer_thickness'] ?></td>
                <td class="bold"><?= $data['coating'] ?></td>
                <td class="bold">RAL <?= $data['type'] == Product::SHEET_TYPE_INTERNAL ? $model['inner_color'] : $model['outer_color'] ?></td>
            </tr>
            <tr class="border-top2 border-bottom2">
                <td>№</td>
                <td>Ширина, мм</td>
                <td>Длина, мм</td>
                <td>Количество, шт.</td>
                <td>м<sup>2</sup></td>
                <td></td>
            </tr>
            <?php foreach ($data['items'] as $key => $item) : ?>
            <tr>
                <td><?= $key + 1 ?></td>
                <td><?= $item['width'] ?></td>
                <td><?= $item['length'] ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= NumberHelper::cropDecimals($item['area']) ?></td>
                <td></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="top-spacing">
        <tbody>
            <tr class="no-border">
                <td style="width: 20%;"></td>
                <td style="width: 20%;"></td>
                <td style="width: 20%;" class="align-right">Всего, м<sup>2</sup></td>
                <td style="width: 20%;" class="bold align-right border-dotted"><?= NumberHelper::cropDecimals($data['area_total']) ?></td>
                <td></td>
            </tr>
            <tr class="no-border">
                <td></td>
                <td></td>
                <td class="align-right">Всего, кг</td>
                <td class="bold align-right border-dotted"><?= NumberHelper::cropDecimals($data['weight_total']) ?></td>
                <td></td>
            </tr>
            <tr class="no-border">
                <td></td>
                <td></td>
                <td class="align-right">Всего, м пог</td>
                <td class="bold align-right border-dotted"><?= NumberHelper::cropDecimals($data['length_total']) ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table class="top-spacing">
        <tbody>
            <tr class="border-top2">
                <td colspan="2"><?= $data['specification'] ?></td>
                <td>№ заказа</td>
                <td><?= $model['number'] ?></td>
                <td></td>
                <td>Дата производства</td>
            </tr>
            <tr>
                <td colspan="2">Заказчик</td>
                <td colspan="3" class="bold"><?= $model['customerName'] ?></td>
                <td><?= !empty($model['stage_release_fact_date'] ?? $model['stage_release_plan_date'])
                        ? Yii::$app->formatter->asDate($model['stage_release_fact_date'] ?? $model['stage_release_plan_date'], 'php:d.m.Y')
                        : '' ?>
                </td>
            </tr>
            <tr class="border-top2">
                <td colspan="2">Номенклатура</td>
                <td colspan="2" class="bold"><?= $model['productName'] ?></td>
                <td class="bold">в пленке</td>
                <td class="bold"><?= $data['direction'] ?></td>
            </tr>
            <tr class="border-top2">
                <td colspan="2">Утеплитель</td>
                <td colspan="2" class="bold"><?= $data['insulation'] ?></td>
                <td class="bold"><?= $model['insulation_thickness_id'] ?></td>
                <td class="bold"></td>
            </tr>
            <tr class="border-top2">
                <td colspan="2">Внешняя</td>
                <td>Толщина, мм</td>
                <td>Покрытие</td>
                <td>Цвет</td>
                <td>м<sup>2</sup></td>
            </tr>
            <tr class="border-top2">
                <td colspan="2" class="bold"><?= $data['type'] == Product::SHEET_TYPE_INTERNAL ? $model['inner_type'] : $model['outer_type'] ?></td>
                <td class="bold"><?= $data['type'] == Product::SHEET_TYPE_INTERNAL ? $model['inner_thickness'] : $model['outer_thickness'] ?></td>
                <td class="bold"><?= $data['coating'] ?></td>
                <td class="bold">RAL <?= $data['type'] == Product::SHEET_TYPE_INTERNAL ? $model['inner_color'] : $model['outer_color'] ?></td>
                <td><?= NumberHelper::cropDecimals($data['area_total']) ?></td>
            </tr>
        </tbody>
    </table>

    <table class="top-spacing">
        <tbody>
            <tr class="no-border">
                <td class="align-right">Всего, шт.</td>
                <td class="bold align-right border-dotted"><?= $data['quantity_total'] ?></td>
                <td class="align-right">Всего, кг</td>
                <td class="bold align-right border-dotted"><?= NumberHelper::cropDecimals($data['weight_total']) ?></td>
                <td class="align-right">Всего, м пог</td>
                <td class="bold align-right border-dotted"><?= NumberHelper::cropDecimals($data['length_total']) ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table class="top-spacing">
        <tbody>
            <tr class="border-top2">
                <td class="border" style="width: 30%;">№ бухты</td>
                <td class="border">Производитель утеплителя</td>
            </tr>
            <tr class="border-top2">
                <td class="border">&nbsp;</td>
                <td class="border">&nbsp;</td>
            </tr>
            <tr>
                <td class="border">&nbsp;</td>
                <td class="border">&nbsp;</td>
            </tr>
        </tbody>
    </table>

    <table class="top-spacing">
        <tbody>
        <tr class="border-top2">
            <td class="border" rowspan="2">Контрольный замер</td>
            <td class="border">Ширина</td>
            <td class="border">Длина</td>
            <td class="border">Ширина</td>
            <td class="border">Длина</td>
        </tr>
        <tr class="border-top2">
            <td class="border">&nbsp;</td>
            <td class="border">&nbsp;</td>
            <td class="border">&nbsp;</td>
            <td class="border">&nbsp;</td>
        </tr>
        </tbody>
    </table>

    <table class="top-spacing">
        <tbody>
        <tr>
            <td style="width: 20%;" class="no-border align-right">Примечание:</td>
            <td class="no-border border-bottom">&nbsp;</td>
        </tr>
        </tbody>
    </table>

    <h4 class="top-spacing">Количество изготовленной продукции сверх нормы</h4>
    <table>
        <tbody>
            <tr class="border-top2">
                <td>Отход, м пог.</td>
                <td>Некондиция, <длина> x <количество></td>
            </tr>
            <tr class="border-top2">
                <td class="border">&nbsp;</td>
                <td class="border">&nbsp;</td>
            </tr>
        </tbody>
    </table>
    <table class="top-spacing">
        <tbody>
            <tr>
                <td style="width: 20%;" class="no-border align-right">Время выполнения:</td>
                <td class="no-border border-bottom">&nbsp;</td>
                <td style="width: 20%;" class="no-border align-right">ФИО оператора:</td>
                <td style="width: 40%;" class="no-border border-bottom">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4" class="no-border">&nbsp;</td>
            </tr>
            <tr>
                <td class="no-border align-right">Место на складе:</td>
                <td class="no-border border-bottom">&nbsp;</td>
                <td class="no-border align-right">Подпись</td>
                <td class="no-border border-bottom">&nbsp;</td>
            </tr>
        </tbody>
    </table>
</body>
</html>