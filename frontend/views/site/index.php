<?php
use yii\helpers\Url;
/* @var $this yii\web\View */


$this->title = 'Rbc news';
?>
<div class="site-index">
    <div class="jumbotron">
        <h2>Парсинг новостей РБК!</h2>
        <p class="lead">Для запуска нажмите кнопку!</p>
        <p>
            <a class="btn btn-lg btn-success" href="<?= Url::toRoute(['site/parse']) ?>">
                Запуск
            </a>
        </p>
    </div>
</div>
