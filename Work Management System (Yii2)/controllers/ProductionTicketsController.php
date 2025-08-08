<?php

namespace app\controllers;

use app\models\MaterialWidth;
use app\models\Order;
use app\models\OrderWProductionStageLink;
use app\models\Product;
use app\models\ProductionStage;
use app\models\search\OrderSearch;
use app\widgets\Flashes;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * ProductionTickets Controller
 */
class ProductionTicketsController extends BaseController
{
    public $modelClass = Order::class;
    public $searchClass = OrderSearch::class;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['preview', 'print', 'save-positions'],
                        'roles' => ['app_orders_index'],
                    ],
                ],
            ],
        ];
    }

    public function actionPreview($id, $stage = '')
    {
        $stage = strtolower($stage);

        if (!isset($stage) || !in_array($stage, ProductionStage::AVAILABLE_STAGES)) {
            Flashes::setError('Указан некорректный этап производства');
            return $this->redirect('preview');
        }

        $model = OrderSearch::findOne($id);

        if (!isset($model)) {
            Flashes::setError('Запись не найдена');
            return $this->redirect('preview');
        }

        $request = Yii::$app->request;
        $this->data['type'] = in_array($request->get('type'), [Product::SHEET_TYPE_INTERNAL, Product::SHEET_TYPE_EXTERNAL])
            ? $request->get('type')
            : Product::SHEET_TYPE_INTERNAL;

        $this->data['positions'] = $this->getTicketPositions($id, $stage);

        if ($stage === ProductionStage::STAGE_SANDWICH) {
            $this->data['sheet_positions'] = $this->getTicketPositions($id, ProductionStage::STAGE_SHEET);
        }

        $this->data['specificationsNumber'] = count($this->data['positions'] ?? []);

        return $this->render(
            'preview-' . $stage,
            [
                'title' => $this->sysTitle(),
                'model' => $model,
                'data'  => $this->data,
            ]
        );
    }

    public function actionPrint($id, $stage = '')
    {
        $stage = strtolower($stage);

        if (!isset($stage) || !in_array($stage, ProductionStage::AVAILABLE_STAGES)) {
            return $this->renderPartial('print-error');
        }

        $mainStageSubQuery = OrderWProductionStageLink::find()
            ->select([
                'order_id',
                'release_plan_date',
                'release_fact_date',
            ])
            ->joinWith('productionStage')
            ->where(['production_stage.code' => $stage])
            ->andWhere(['order_id' => $id]);

        $sheetStageSubQuery = OrderWProductionStageLink::find()
            ->select([
                'order_id',
                'inner_type',
                'outer_type',
                'inner_color',
                'outer_color',
                'inner_thickness',
                'outer_thickness',
                'ticket_positions',
            ])
            ->joinWith('productionStage')
            ->where(['production_stage.code' => ProductionStage::STAGE_SHEET])
            ->andWhere(['order_id' => $id]);

        $archStageSubQuery = OrderWProductionStageLink::find()
            ->select([
                'order_id',
                'outer_color',
                'outer_thickness',
            ])
            ->joinWith('productionStage')
            ->where(['production_stage.code' => ProductionStage::STAGE_ARCH4521])
            ->andWhere(['order_id' => $id]);

        $model = (new Query())
            ->select([
                'order.*',
                'organization.name AS customerName',
                'product_category.name AS productName',
                'sheet_stage.inner_type AS inner_type',
                'sheet_stage.outer_type AS outer_type',
                'sheet_stage.inner_color AS inner_color',
                'sheet_stage.outer_color AS outer_color',
                'sheet_stage.inner_thickness AS inner_thickness',
                'sheet_stage.outer_thickness AS outer_thickness',
                'sheet_stage.ticket_positions AS ticket_positions',
                'arch_stage.outer_color AS arch_color',
                'arch_stage.outer_thickness AS arch_thickness',
                'stages.release_plan_date AS stage_release_plan_date',
                'stages.release_fact_date AS stage_release_fact_date',
                'product_category.name as productCategoryName',
            ])
            ->from('order')
            ->leftJoin('organization', 'order.customer_id = organization.id')
            ->leftJoin('product', 'order.product_id = product.id')
            ->leftJoin('material_width', 'order.material_width_id = material_width.id')
            ->leftJoin('insulation_thickness', 'order.insulation_thickness_id = insulation_thickness.id')
            ->leftJoin('product_category', 'product_category.id = product.category_id')
            ->leftJoin(['stages' => $mainStageSubQuery], 'order.id = stages.order_id')
            ->leftJoin(['sheet_stage' => $sheetStageSubQuery], 'order.id = sheet_stage.order_id')
            ->leftJoin(['arch_stage' => $archStageSubQuery], 'order.id = arch_stage.order_id')
            ->where(['order.id' => $id])
            ->one()
        ;

        if (!isset($model)) {
            Flashes::setError('Запись не найдена');
            return $this->redirect2Referrer();
        }

        $stages = ArrayHelper::map(
            (new Query())
                ->select(['product_w_production_stage_link.id', 'production_stage.code'])
                ->from('product_w_production_stage_link')
                ->leftJoin('production_stage', 'product_w_production_stage_link.production_stage_id = production_stage.id')
                ->where(['product_id' => $model['product_id']])
                ->andWhere(['production_stage.code' => $stage])
                ->all(),
            'id',
            'code'
        );

        if (!in_array($stage, array_values($stages))) {
            Flashes::setError('Указан некорректный этап производства');
            return $this->redirect2Referrer();
        }

        $request = Yii::$app->request;

        switch ($stage) {
            case ProductionStage::STAGE_INSULATION:
                $this->prepareFormInsulation($model, $request);
                break;
            case ProductionStage::STAGE_ARCH4521:
                $this->prepareFormArch4521($model, $request);
                break;
            case ProductionStage::STAGE_BENDING:
                $this->prepareFormBending($model, $request);
                break;
            case ProductionStage::STAGE_MM35:
                $this->prepareFormMm35($model, $request);
                break;
            case ProductionStage::STAGE_PL:
                $this->prepareFormPl($model, $request);
                break;
            case ProductionStage::STAGE_SHEET:
                $this->prepareFormSheet($model, $request);
                break;
            case ProductionStage::STAGE_SANDWICH:
                $this->prepareFormSandwich($model, $request);
                break;
        }

        return $this->renderPartial(
            'print-' . $stage,
            [
                'model' => $model,
                'data'  => $this->data,
            ]
        );
    }

    public function actionSavePositions($id, $stage = '')
    {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect2Referrer();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = OrderSearch::findOne($id);
        if (!isset($model)) {
            return ['result' => false, 'error' => 'Заказ не найден.'];
        }

        if ($stage === ProductionStage::STAGE_SANDWICH) {
            $orderStage = OrderWProductionStageLink::find()
                ->joinWith('productionStage')
                ->where(['production_stage.code' => ProductionStage::STAGE_SANDWICH])
                ->andWhere(['order_id' => $model['id']])
                ->one()
            ;

            $ticketPositions = Yii::$app->request->post('specifications') ?? [];
            $orderStage->updateAttributes([
                'ticket_positions' => json_encode($ticketPositions),
            ]);
        }

        return ['result' => true];
    }

    private function prepareFormInsulation($model, $request)
    {
        $insulations = (new Query())
            ->select('insulation.name')
            ->from('order_w_production_stage_w_insulation_link')
            ->leftJoin('insulation', 'insulation.id = order_w_production_stage_w_insulation_link.insulation_id')
            ->leftJoin('order_w_production_stage_link', 'order_w_production_stage_link.id = order_w_production_stage_w_insulation_link.order_production_stage_id')
            ->where(['order_w_production_stage_link.order_id' => $model['id']])
            ->column();

        $density = (float)($request->get('density') ?: 0);
        $weightM3 = (float)($request->get('weight_m3') ?: 0);

        $widthList = $request->get('width') ?? [];
        $lengthList = $request->get('length') ?? [];
        $quantityList = $request->get('quantity') ?? [];

        $items = [];

        foreach ($widthList as $key => $width) {
            $items[$key]['width'] = (float)($width ?: 0);
            $items[$key]['length'] = (float)($lengthList[$key] ?: ($model['max_length'] ?? 0));
            $items[$key]['height'] = Product::INSULATION_HEIGHT;
            $items[$key]['quantity'] = (int)($quantityList[$key] ?: 1);
            $items[$key]['volume'] = $items[$key]['width']
                * $items[$key]['length']
                * $items[$key]['height'] /* fixed height*/
                / 1000000000 /* mm3 -> m3 */
                * $items[$key]['quantity']
            ;
        }

        if (empty($items)) {
            $items[] = [
                'width' => 0,
                'length' => $model['max_length'] ?? 0,
                'height' => Product::INSULATION_HEIGHT,
                'quantity' => 1,
                'volume' => 0,
            ];
        }

        $volumeTotal = array_sum(array_column($items, 'volume'));

        $this->data = [
            'specification' => $request->get('specification') ?? 'Спецификация 1 из 1',
            'direction' => $request->get('direction') ?? 'M1',
            'insulation' => implode(', ', $insulations),
            'comment' => $request->get('comment'),
            'items' => $items,
            'density' => $density,
            'weight_m3' => $weightM3,
            'calculation' => $weightM3,
            'volume_total' => round($volumeTotal, 3),
            'weight_total' => round($volumeTotal * $weightM3, 3),
        ];
    }

    private function prepareFormArch4521($model, $request)
    {
        $widthList = $request->get('width') ?? [];
        $lengthList = $request->get('length') ?? [];
        $quantityList = $request->get('quantity') ?? [];

        $items = [];

        foreach ($widthList as $key => $width) {
            $items[$key]['width'] = (float)($width ?: 0);
            $items[$key]['length'] = (float)($lengthList[$key] ?: 0);
            $items[$key]['quantity'] = (int)($quantityList[$key] ?: 1);
            $items[$key]['area'] = $items[$key]['width']
                * $items[$key]['length']
                * $items[$key]['quantity']
                / 1000000 /* mm2 -> m2 */
            ;
        }

        if (empty($items)) {
            $items[] = [
                'width' => 0,
                'length' => 0,
                'quantity' => 1,
                'area' => 0,
            ];
        }

        $areaTotal = array_sum(array_column($items, 'area'));
        $lengthTotal = array_sum(array_map(
            function ($item) {
                return $item['length'] / 1000 * $item['quantity'];  /* mm -> m */
            },
            $items
        ));

        $this->data = [
            'specification' => $request->get('specification') ?? 'Спецификация 1 из 1',
            'direction' => $request->get('direction') ?? 'M1',
            'coating' => $request->get('coating') ?? 'Полиэстер',
            'items' => $items,
            'area_total' => round($areaTotal, 3),
            'weight_total' => round($areaTotal * 0.55 * 7.85 * 2, 3),
            'length_total' => round($lengthTotal, 3),
            'quantity_total' => array_sum(array_column($items, 'quantity')),
        ];
    }

    private function prepareFormBending($model, $request)
    {
        $density = (float)($request->get('density') ?: 0);
        $weightM3 = (float)($request->get('weight_m3') ?: 0);

        $widthList = $request->get('width') ?? [];
        $lengthList = $request->get('length') ?? [];
        $quantityList = $request->get('quantity') ?? [];

        $items = [];

        foreach ($widthList as $key => $width) {
            $items[$key]['width'] = (float)($width ?: 0);
            $items[$key]['length'] = (float)($lengthList[$key] ?: 0);
            $items[$key]['quantity'] = (int)($quantityList[$key] ?: 1);
            $items[$key]['area'] = $items[$key]['width']
                * $items[$key]['length']
                * $items[$key]['quantity']
                / 1000000 /* mm2 -> m2 */
            ;
        }

        if (empty($items)) {
            $items[] = [
                'width' => 0,
                'length' => 0,
                'quantity' => 1,
                'area' => 0,
            ];
        }

        $areaTotal = array_sum(array_column($items, 'area'));

        $this->data = [
            'specification' => $request->get('specification') ?? 'Спецификация 1 из 1',
            'direction' => $request->get('direction') ?? 'M1',
            'thickness' => $request->get('thickness') ?: '',
            'coating' => $request->get('coating') ?? 'Полиэстер',
            'outer_color' => $request->get('outer_color') ?? '',
            'items' => $items,
            'density' => $density,
            'weight_m3' => $weightM3,
            'area_total' => round($areaTotal, 3),
            'weight_total' => round($areaTotal * 0.7 * 7.85, 3),
            'length_total' => round($areaTotal / 1.25, 3),
            'quantity_total' => array_sum(array_column($items, 'quantity')),
        ];
    }

    private function prepareFormMm35($model, $request)
    {
        $widthList = $request->get('width') ?? [];
        $lengthList = $request->get('length') ?? [];
        $quantityList = $request->get('quantity') ?? [];

        $items = [];

        foreach ($widthList as $key => $width) {
            $items[$key]['width'] = (float)($width ?: 0);
            $items[$key]['length'] = (float)($lengthList[$key] ?: 0);
            $items[$key]['quantity'] = (int)($quantityList[$key] ?: 1);
            $items[$key]['area'] = $items[$key]['width']
                * $items[$key]['length']
                * $items[$key]['quantity']
                / 1000000 /* mm2 -> m2 */
            ;
        }

        if (empty($items)) {
            $items[] = [
                'width' => 0,
                'length' => 0,
                'quantity' => 1,
                'area' => 0,
            ];
        }

        $areaTotal = array_sum(array_column($items, 'area'));

        $this->data = [
            'type' => Product::MM35_TYPES[$request->get('type') ?? 0],
            'specification' => $request->get('specification') ?? 'Спецификация 1 из 1',
            'direction' => $request->get('direction') ?? 'M1',
            'coating' => $request->get('coating') ?? 'Полиэстер',
            'items' => $items,
            'area_total' => round($areaTotal, 3),
            'weight_total' => round($areaTotal * 0.7 * 7.85, 3),
            'length_total' => round($areaTotal / 1.25, 3),
            'quantity_total' => array_sum(array_column($items, 'quantity')),
        ];
    }

    private function prepareFormPl($model, $request)
    {
        $widthList = $request->get('width') ?? [];
        $lengthList = $request->get('length') ?? [];
        $quantityList = $request->get('quantity') ?? [];

        $items = [];

        foreach ($widthList as $key => $width) {
            $items[$key]['width'] = (float)($width ?: 0);
            $items[$key]['length'] = (float)($lengthList[$key] ?: 0);
            $items[$key]['quantity'] = (int)($quantityList[$key] ?: 1);
            $items[$key]['area'] = $items[$key]['width']
                * $items[$key]['length']
                * $items[$key]['quantity']
                / 1000000 /* mm2 -> m2 */
            ;
        }

        if (empty($items)) {
            $items[] = [
                'width' => 0,
                'length' => 0,
                'quantity' => 1,
                'area' => 0,
            ];
        }

        $areaTotal = array_sum(array_column($items, 'area'));

        $this->data = [
            'specification' => $request->get('specification') ?? 'Спецификация 1 из 1',
            'direction' => $request->get('direction') ?? 'M1',
            'coating' => $request->get('coating') ?? 'Полиэстер',
            'items' => $items,
            'area_total' => round($areaTotal, 3),
            'weight_total' => round($areaTotal * 0.7 * 7.85, 3),
            'length_total' => round($areaTotal / 1.25, 3),
            'quantity_total' => array_sum(array_column($items, 'quantity')),
        ];
    }

    /**
     * Логика работы с листами.
     * Сначала должен быть заполнен бланк внутреннего листа, позиции редактировать можно как угодно,
     * затем эти позиции из него сохраняются в виде JSON в поле order_w_production_stage_link.ticket_positions.
     * В бланк внешнего листа позиции загружаются как есть, редактировать их в бланке нельзя.
     * В бланк СП тоже загружаются эти же позиции, редактировать можно только количества,
     * поэтому для одного заказа может быть несколько бланков СП с разным количеством, в сумме эти количества
     * по каждой позиции равны количеству соответствующей позиции во внутреннем листе.
     *
     * Формат поля ticket_positions: JSON массив элементов, каждый - отдельная спецификация (для внутр. листа - только одна,
     * для СП может быть несколько), в ней - массив позиций, у каждого элемента есть поля length, quantity.
     */
    private function prepareFormSheet($model, $request)
    {
        $insulations = (new Query())
            ->select('insulation.name')
            ->from('order_w_production_stage_w_insulation_link')
            ->leftJoin('insulation', 'insulation.id = order_w_production_stage_w_insulation_link.insulation_id')
            ->leftJoin('order_w_production_stage_link', 'order_w_production_stage_link.id = order_w_production_stage_w_insulation_link.order_production_stage_id')
            ->where(['order_w_production_stage_link.order_id' => $model['id']])
            ->column();

        $widthList = ArrayHelper::map(
            MaterialWidth::find()
                ->all(),
            'id',
            'corrected_value'
        );

        $lengthList = $request->get('length') ?? [];
        $quantityList = $request->get('quantity') ?? [];

        $ticketPositions = $this->getTicketPositions($model['id'], ProductionStage::STAGE_SHEET);
        $items = $ticketPositions[0] ?? [];

        if ($request->get('type') === Product::SHEET_TYPE_INTERNAL && !empty($lengthList)) {
            $items = [];
            foreach ($lengthList as $key => $length) {
                $items[$key]['width'] = $widthList[$model['material_width_id']] ?? 0;
                $items[$key]['length'] = (float)($length ?: 0);
                $items[$key]['quantity'] = (int)($quantityList[$key] ?: 1);
                $items[$key]['area'] = $items[$key]['width']
                    * $items[$key]['length']
                    * $items[$key]['quantity']
                    / 1000000 /* mm2 -> m2 */
                ;
            }
        }

        if (empty($items)) {
            $items[] = [
                'width' => $widthList[$model['material_width_id']] ?? 0,
                'length' => 0,
                'quantity' => 1,
                'area' => 0,
            ];
        }

        $areaTotal = array_sum(array_column($items, 'area'));

        $this->data = [
            'type' => in_array($request->get('type'), [Product::SHEET_TYPE_INTERNAL, Product::SHEET_TYPE_EXTERNAL])
                ? $request->get('type')
                : Product::SHEET_TYPE_INTERNAL,
            'specification' => $request->get('specification') ?? 'Спецификация 1 из 1',
            'direction' => $request->get('direction') ?? 'M1',
            'insulation' => implode(', ', $insulations),
            'coating' => $request->get('coating') ?? 'Полиэстер',
            'items' => $items,
            'area_total' => round($areaTotal, 3),
            'weight_total' => round($areaTotal * 0.5 * 7.85, 3),
            'length_total' => round($areaTotal / 1.25, 3),
            'quantity_total' => array_sum(array_column($items, 'quantity')),
        ];

        if ($request->get('type') === Product::SHEET_TYPE_INTERNAL) {
            $sheetStage = OrderWProductionStageLink::find()
                ->joinWith('productionStage')
                ->where(['production_stage.code' => ProductionStage::STAGE_SHEET])
                ->andWhere(['order_id' => $model['id']])
                ->one()
            ;

            $newTicketPositions = json_encode([
                0 => $this->data['items']
            ]);

            if ($newTicketPositions != $sheetStage->ticket_positions) {
                $sheetStage->updateAttributes([
                    'ticket_positions' => $newTicketPositions,
                ]);

                $sandwichStage = OrderWProductionStageLink::find()
                    ->joinWith('productionStage')
                    ->where(['production_stage.code' => ProductionStage::STAGE_SANDWICH])
                    ->andWhere(['order_id' => $model['id']])
                    ->one()
                ;
                $sandwichStage->updateAttributes([
                    'ticket_positions' => $newTicketPositions,
                ]);
            }
        }
    }

    private function prepareFormSandwich($model, $request)
    {
        $insulations = (new Query())
            ->select('insulation.name')
            ->from('order_w_production_stage_w_insulation_link')
            ->leftJoin('insulation', 'insulation.id = order_w_production_stage_w_insulation_link.insulation_id')
            ->leftJoin('order_w_production_stage_link', 'order_w_production_stage_link.id = order_w_production_stage_w_insulation_link.order_production_stage_id')
            ->where(['order_w_production_stage_link.order_id' => $model['id']])
            ->column();

        $quantityList = $request->get('quantity') ?? [];
        $specificationNumber = (int)($request->get('specification') ?? 1);

        $ticketPositions = $this->getTicketPositions($model['id'], ProductionStage::STAGE_SANDWICH);
        $items = $ticketPositions[$specificationNumber - 1] ?? [];

        if (!empty($quantityList)) {
            $items = [];
            foreach ($quantityList as $key => $quantity) {
                $items[$key]['width'] = (int)($ticketPositions[$specificationNumber - 1][$key]['width'] ?: 0);
                $items[$key]['length'] = (float)($ticketPositions[$specificationNumber - 1][$key]['length'] ?: 0);
                $items[$key]['quantity'] = (int)($quantity ?: 0);
                $items[$key]['area'] = $items[$key]['width']
                    * $items[$key]['length']
                    * $items[$key]['quantity']
                    / 1000000 /* mm2 -> m2 */
                ;
            }
        }

        if (empty($items)) {
            $items[] = [
                'width' => $model['material_width_id'] ?? 0,
                'length' => $model['max_length'] ?? $model['min_length'],
                'quantity' => 1,
                'area' => ($model['material_width_id'] ?? 0) * ($model['max_length'] ?? $model['min_length']) / 1000000,
            ];
        }

        $areaTotal = array_sum(array_column($items, 'area'));
        $lengthTotal = array_sum(array_map(
            function ($item) {
                return $item['length'] / 1000 * $item['quantity'];  /* mm -> m */
            },
            $items
        ));

        $this->data = [
            'specification' => sprintf('Спецификация %d из %d', $specificationNumber, count($ticketPositions)),
            'direction' => $request->get('direction') ?? 'M1',
            'insulation' => implode(', ', $insulations),
            'inner_coating' => $request->get('inner_coating') ?? 'Полиэстер',
            'outer_coating' => $request->get('outer_coating') ?? 'Полиэстер',
            'items' => $items,
            'area_total' => round($areaTotal, 3),
            'weight_total' => round($areaTotal * 0.5 * 7.85 * 2, 3),
            'length_total' => round($lengthTotal, 3),
            'quantity_total' => array_sum(array_column($items, 'quantity')),
        ];

        $orderStage = OrderWProductionStageLink::find()
            ->joinWith('productionStage')
            ->where(['production_stage.code' => ProductionStage::STAGE_SANDWICH])
            ->andWhere(['order_id' => $model['id']])
            ->one()
        ;

        $ticketPositions[$specificationNumber - 1] = $this->data['items'];
        $orderStage->updateAttributes([
            'ticket_positions' => json_encode($ticketPositions),
        ]);
    }

    private function getTicketPositions($orderId, $stage)
    {
        if (!in_array($stage, [ProductionStage::STAGE_SHEET, ProductionStage::STAGE_SANDWICH])) {
            return [];
        }

        if ($stage === ProductionStage::STAGE_SANDWICH) {
            $sheetStage = OrderWProductionStageLink::find()
                ->joinWith('productionStage')
                ->where(['production_stage.code' => ProductionStage::STAGE_SANDWICH])
                ->andWhere(['order_id' => $orderId])
                ->one()
            ;
        }

        if (empty($sheetStage->ticket_positions)) {
            $sheetStage = OrderWProductionStageLink::find()
                ->joinWith('productionStage')
                ->where(['production_stage.code' => ProductionStage::STAGE_SHEET])
                ->andWhere(['order_id' => $orderId])
                ->one()
            ;
        }

        if (!$sheetStage instanceof OrderWProductionStageLink) {
            return [];
        }

       return json_decode($sheetStage->ticket_positions ?? '', true);
    }
}
