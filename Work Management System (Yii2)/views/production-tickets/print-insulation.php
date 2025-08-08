<?php

/* @var $this yii\web\View */
/* @var $model array */
/* @var $data array */

use app\helpers\NumberHelper;

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Печать задания на производство утеплителя</title>
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
    <h3>Производственное задание на изготовление утеплителя</h3>
    <table class="top-spacing">
        <tbody>
            <tr class="border-top2">
                <td colspan="2"><?= $data['specification'] ?></td>
                <td>№ заказа</td>
                <td><?= $model['number'] ?></td>
                <td></td>
                <td rowspan="5" class="direction"><?= $data['direction'] ?></td>
            </tr>
            <tr>
                <td colspan="2">Заказчик</td>
                <td colspan="2" class="bold"><?= $model['customerName'] ?></td>
                <td><?= $model['number'] ?></td>
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
            <tr class="border-top2 border-bottom2">
                <td>№</td>
                <td>Ширина, мм</td>
                <td>Длина, мм</td>
                <td>Высота, мм</td>
                <td>Количество, шт.</td>
                <td>м<sup>3</sup></td>
            </tr>
            <?php foreach ($data['items'] as $key => $item) : ?>
            <tr>
                <td><?= $key + 1 ?></td>
                <td><?= $item['width'] ?></td>
                <td><?= $item['length'] ?></td>
                <td><?= $item['height'] ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= NumberHelper::cropDecimals($item['volume']) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="6" class="text-center"><?= $data['comment'] ?></td>
            </tr>
        </tbody>
    </table>
    <table class="top-spacing">
        <tbody>
            <tr class="no-border">
                <td style="width: 20%;" class="align-right">Плотность</td>
                <td style="width: 20%;" class="bold align-right border-dotted"><?= $data['density'] ?></td>
                <td style="width: 20%;" class="align-right">Расчет</td>
                <td style="width: 20%;" class="bold align-right border-dotted"><?= $data['calculation'] ?></td>
                <td></td>
            </tr>
            <tr class="no-border">
                <td class="align-right">Вес м<sup>3</sup></td>
                <td class="bold align-right border-dotted"><?= $data['weight_m3'] ?></td>
                <td class="align-right">Всего, м<sup>3</sup></td>
                <td class="bold align-right border-dotted"><?= NumberHelper::cropDecimals($data['volume_total']) ?></td>
                <td></td>
            </tr>
            <tr class="no-border">
                <td></td>
                <td></td>
                <td class="align-right">Всего, кг</td>
                <td class="bold align-right border-dotted"><?= NumberHelper::cropDecimals($data['weight_total']) ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <table class="top-spacing">
        <tbody>
            <tr class="border-top2">
                <td class="border">№ палеты/место на складе</td>
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
                <td>Некондиция, <длина> x <ширина> x <высота> x <количество></td>
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