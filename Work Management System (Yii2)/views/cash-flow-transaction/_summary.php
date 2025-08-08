<div class="summary-block">
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="summary-item">
                <h4>Остаток в кассе:</h4>
                <p><?= Yii::$app->formatter->asDecimal($searchModel->getCashBalance(), 2) ?></p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="summary-item">
                <h4>Итого все доходы за период:</h4>
                <p><?= Yii::$app->formatter->asDecimal($searchModel->getTotalIncome(), 2) ?></p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="summary-item">
                <h4>Итого все расходы за период:</h4>
                <p><?= Yii::$app->formatter->asDecimal($searchModel->getTotalExpense(), 2) ?></p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="summary-item">
                <h4>Сальдо за период:</h4>
                <p><?= Yii::$app->formatter->asDecimal($searchModel->getBalance(), 2) ?></p>
            </div>
        </div>
    </div>
</div>
