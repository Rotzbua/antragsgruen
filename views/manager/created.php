<?php
use app\models\db\Site;
use yii\helpers\Html;
use yii\helpers\Url;

$controller = $this->context;

/**
 * @var $this yii\web\View
 * @var Site $site
 * @var string $login_id
 * @var string $login_code
 */

$redirectUrl = Url::toRoute(['consultation/index', 'subdomain' => $site->subdomain]);
?>
<h1>Veranstaltung angelegt</h1>
<div class="content">
    <div class="alert alert-success" role="alert">
        Die Veranstaltung wurde angelegt.
    </div>
    <?php
    echo Html::beginForm(
        [
            'user/loginbyredirecttoken', 'subdomain' => $site->subdomain, 'login' => $login_id,
                                         'login_sec' => $login_code, 'redirect' => $redirectUrl
        ]
    );
    ?>
    <br><br>

    <div style="text-align: center;">
        <button type="submit" class="btn btn-primary">Zur neu angelegten Veranstaltung</button>
    </div>
    <?php
    echo Html::endForm();
    ?>
</div>