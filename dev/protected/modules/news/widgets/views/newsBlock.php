<?php
/**
 * @var News[] $news
 */
?>

<section class="module">


    <h3 class="moduleHeader"><?php echo Yii::t('newsModule.common', 'News') ?></h3>

	<div class="accordion" id="newsAccordion">

	<?php foreach ( $news AS $key => $new ) { ?>
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#newsAccordion" href="#collapse<?php echo $new->getId() ?>">
	                <abbr title="<?php echo $new->getCtime('d.m.Y H:i'); ?>"><?php echo TimeHelper::timeAgoInWords($new->getCtime()) ?></abbr> - <?php echo $new->getTitle() ?>
                </a>
            </div>
            <div id="collapse<?php echo $new->getId() ?>" class="accordion-body collapse<?php echo ( $key == 0 ? ' in' : '') ?>">
                <div class="accordion-inner">
	                <?php echo $new->getText() ?>
                </div>
            </div>
        </div>


	<?php } ?>

	</div>
</section>