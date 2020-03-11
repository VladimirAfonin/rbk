<?php

/* @var $this yii\web\View */
/* @var $news \frontend\models\News */

use yii\helpers\{Url};

$this->title = 'Новость';
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['news']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="body-content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?= $news->title ?>
                    </h3>
                </div>
                <div class="panel-body">
                    <img src="<?= $news->image_path ?>" alt="<?= $news->title ?>" class="img-thumbnail">
                  <?= $news->description; ?>
                </div>
                <div class="panel-footer">
                    <?= $news->category->title ?>, <?= $news->fullDateForNews ?>
                </div>
            </div>

            <a href="<?= Url::toRoute('/site/news'); ?>">к списку новостей &raquo;</a>
        </div>
    </div>
</div>
