<?php

/* @var $this yii\web\View */

use app\models\database\Cottage;
use nirvana\showloading\ShowLoadingAsset;
use yii\helpers\Url;

ShowLoadingAsset::register($this);

/* @var $cottages Cottage[] */

$this->title = 'Участки';
?>
<div class="site-index">


    <div class="body-content">

        <div class="row">

            <div class="col-sm-12 text-center margin">
                <div class="btn-group">
                    <a href="<?=Url::toRoute(['site/mass-bill', 'type' => 'membership']);?>" target="_blank" class="btn btn-default"><span class="text-success">Выставить счёта по членским взносам</span>
                    </a>
                    <a href="<?=Url::toRoute(['site/mass-bill', 'type' => 'target']);?>" target="_blank" class="btn btn-default"><span class="text-success">Выставить счёта по целевым взносам</span>
                    </a>
                </div>
            </div>

            <div class="col-sm-12 text-center">
                <button class="btn btn-success" id="addNewBtn">Добавить участок</button>
            </div>
            <div class="col-lg-12">
                <?php
                if ($cottages !== null) {
                    foreach ($cottages as $cottage) {
                        echo "<a href='/show/$cottage->num' class='btn btn-default cottage'><span class='text-success'>$cottage->num</span></a>";
                    }
                }
                ?>
            </div>
        </div>

        <div class="col-sm-12 text-center margin">
            <div class="btn-group">
                <a href="<?=Url::toRoute(['utils/save-register', 'type' => 'membership']);?>" target="_blank" class="btn btn-default"><span class="text-success">Сохранить данные для реестра</span>
                </a>
            </div>
        </div>
    </div>
</div>