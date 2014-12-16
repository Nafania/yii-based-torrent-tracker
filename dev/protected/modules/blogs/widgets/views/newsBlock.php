<?php
/**
 * @var modules\blogs\models\BlogPost[] $news
 */
?>

<section class="module">


    <h3 class="moduleHeader"><?php echo Yii::t('blogsModule.common', 'Новости') ?></h3>

	<div class="accordion" id="newsAccordion">

	<?php foreach ( $news AS $key => $new ) { ?>
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#newsAccordion" href="#collapse<?php echo $new->getId() ?>"><abbr title="<?php echo $new->getCtime('d.m.Y H:i'); ?>"><?php echo TimeHelper::timeAgoInWords($new->getCtime()) ?></abbr></a>
	            <?= CHtml::link($new->getTitle(), $new->getUrl()); ?>
                <?php
          		    if ( $commentsCount = $new->commentsCount->count ) {
          			    echo CHtml::link('<i class="icon-comment"></i> ' . $commentsCount, CMap::mergeArray($new->getUrl(), ['#' => 'comments']));
          		    }
                ?>
            </div>
            <div id="collapse<?php echo $new->getId() ?>" class="accordion-body collapse<?php echo ( $key == 0 ? ' in' : '') ?>">
                <div class="accordion-inner">
	                <?php echo StringHelper::cutStr($new->getText(), 200, CHtml::link(Yii::t('blogsModule.common', 'читать далее >>>'), $new->getUrl())) ?>
                </div>
            </div>
        </div>


	<?php } ?>

	</div>
</section>