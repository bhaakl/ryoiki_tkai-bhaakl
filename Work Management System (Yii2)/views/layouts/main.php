<?php

use app\assets\AppExtraAsset;
use app\assets\DisableDoubleSendingAsset;
use app\assets\ExtFormsInitAsset;
use app\theme\skote\assets\SkoteAsset;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var View $this */

SkoteAsset::register($this);
DisableDoubleSendingAsset::register($this);
ExtFormsInitAsset::register($this);
AppExtraAsset::register($this);
$isLogged = !Yii::$app->user->isGuest;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php $this->registerCsrfMetaTags() ?>
    <base href="<?= Url::to(['/'], true) ?>"/>
    <title><?= Html::encode($this->title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <?php $this->head() ?>
</head>

<body data-topbar="dark" data-layout="horizontal">
<?php $this->beginBody() ?>

<div id="layout-wrapper">

    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">

                <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item waves-effect waves-light" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                    <i class="fa fa-fw fa-bars"></i>
                </button>

                <div class="navbar-brand-box">
                    <a href="<?= Url::to(['/']) ?>" class="logo font-size-16 text-white">
                         <?= Yii::$app->name ?>
                    </a>
                </div>

            </div>

            <div class="d-flex">

                <div class="dropdown d-none d-lg-inline-block ms-1">
                    <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                        <i class="bx bx-fullscreen"></i>
                    </button>
                </div>

                <?php if ($isLogged) { ?>
                <div class="dropdown d-inline-block" id="profile-menu">
                    <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php $name =  (string)Yii::$app->user->identity ?>
                        <img class="rounded-circle header-profile-user" src="<?= Yii::$app->user->identity->avatar ?>"
                             alt="<?= $name ?>">
                        <span class="d-none d-xl-inline-block ms-1" key="t-henry"><?= $name ?></span>
                        <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                    </button>
                    <div id="page-header-dropdown" class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="<?= Url::to(['/site/profile']) ?>"><i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile">Профиль</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="<?= Url::to(['/site/logout']) ?>"><i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span key="t-logout">Выход</span></a>
                    </div>
                </div>
                <?php } ?>

            </div>
        </div>
    </header>

    <?php if ($isLogged) { ?>
    <div class="topnav">
        <div class="container-fluid">
            <?= \app\theme\skote\widgets\Menu::widget(['id' => 'main-menu']) ?>
        </div>
    </div>
    <?php } ?>

    <div class="main-content">

        <div class="page-content admin100">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18"><?= $this->title ?></h4>

                            <div class="page-title-right">
                                <?= \app\theme\skote\widgets\Breadcrumbs::widget(['homeLink' => ['url' => '/', 'label' => '<i class="fa fa-home"></i>']]) ?>
                            </div>

                        </div>
                    </div>
                </div>

                <?= \app\widgets\Flashes::widget() ?>

                <?= $content ?>

            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <?= date('Y') ?> © <?= Yii::$app->name ?>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">
                            ...
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

</div>

<?php $this->endBody() ?>
<style>
    #overlay{
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
    }
    #overlay img {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
</style>
<div id="overlay">
    <img src="data:image/gif;base64,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" alt="Loading..." />
</div>
<script>
    $(document).ready(function() {
        $(document).ajaxStart(function() {
            $('#overlay').show();
        });
        $(document).ajaxStop(function() {
            $('#overlay').hide();
        });
    });
</script>
</body>
</html>
<?php $this->endPage() ?>
