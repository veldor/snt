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
                    <a href="<?=Url::toRoute(['site/mass-bill', 'type' => 'membership']) ?>" target="_blank" class="btn btn-default"><span class="text-success">Выставить счёта по членским взносам</span>
                    </a>
                    <a href="<?=Url::toRoute(['site/mass-bill', 'type' => 'target']) ?>" target="_blank" class="btn btn-default"><span class="text-success">Выставить счёта по целевым взносам</span>
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
                        echo "<a href='/show/$cottage->num' class='btn btn-default cottage' data-cottage-id='cottage_{$cottage->num}'><span class='text-success'>$cottage->num</span></a>";
                    }
                }
                ?>
            </div>
        </div>

        <div class="col-sm-12 text-center margin">
            <div class="btn-group">
                <a href="<?=Url::toRoute(['utils/save-register', 'type' => 'membership']) ?>" target="_blank" class="btn btn-default"><span class="text-success">Сохранить данные для реестра</span>
                </a>
            </div>
        </div>
    </div>
</div>


<?php
$referer =  $_SERVER['HTTP_REFERER'];
if(strpos($referer, 'http://linda.snt/show/') === 0){
    $cottageNumber = urldecode(substr($referer, 22));
    echo "<span class=\"hidden\" id=\"scrollTo\">$cottageNumber</span>";
    ?>
    <script>
        let container = $('span#scrollTo');
        if(container.length === 1){
            let cottageNumber = container.text();
            let currentLink = $('a.cottage[data-cottage-id="cottage_' + cottageNumber + '"]');
            if(currentLink.length === 1){
                let destination =  currentLink.offset().top;
                $('html').animate({ scrollTop: destination - 60 }, 500);
                currentLink.addClass('last-clicked');
            }
        }
    </script>
<?php
}