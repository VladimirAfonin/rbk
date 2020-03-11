<?php

/* @var $this yii\web\View */
/* @var $news \frontend\models\News */

use yii\helpers\{Html, StringHelper, Url};

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="body-content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <?php if($news): ?>
                <?php foreach($news as $item): ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <?= $item->title ?>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <?= StringHelper::truncate($item->description, 170, '...'); ?>
                            <?php if($item->description): ?>
                                <br>
                                <br>
                                <?= Html::a('подробнее &raquo;', Url::toRoute(['/site/one', 'id' => $item->id]), ['target' => '_blank']) ?>
                            <?php endif; ?>
                        </div>
                        <div class="panel-footer">
                            <?= $item->category->title ?>, <?= $item->fullDateForNews ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Новостей не найдено!</p>
            <?php endif; ?>
        </div>
    </div>
</div>
