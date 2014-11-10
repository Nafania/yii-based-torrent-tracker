<?php
/**
 * @var $parsers    ReviewInterface[]
 * @var $categories Category[]
 */
?>
<?php
$cs = Yii::app()->getClientScript();
$cs->registerCssFile(Yii::app()->getModule('reviews')->getAssetsUrl() . '/css/styles.css');
?>

<div class="column span-12">
    <div class="module actions">
        <h2><a href="#" class="section"><?php echo Yii::t('reviewsModule.common', 'Список парсеров'); ?></a></h2>
	    <?php foreach ( $parsers as $parser ) {
		    echo '<div class="row">';
		    echo $parser->getTitle();
		    echo '<ul>';
		    foreach ( $parser->getNeededFields() AS $key => $val ) {
			    $this->beginWidget('zii.widgets.jui.CJuiDraggable',
				    array('tagName' => 'li'));
			    echo $val;
			    $this->endWidget();
		    }
		    echo '</ul>';
		    echo '</div>';
	    } ?>
    </div>
</div>

<div class="column span-12">
    <div class="module actions">
        <h2><a href="#" class="section"><?php echo Yii::t('reviewsModule.common', 'Список парсеров'); ?></a></h2>
	    <?php foreach ( $categories as $category ) {
		    echo '<div class="row">';
		    echo $category->getTitle();
		    echo '<ul>';
		    foreach ( $category->attrs AS $attribute ) {
			    $this->beginWidget('zii.widgets.jui.CJuiDroppable',
				    array(
					    'tagName' => 'li',
					    'htmlOptions' => array(
						    'data-id' => $attribute->getId(),
					    ),
					    'options' => array(
						    'accept' => '.ui-draggable',
						    'drop'   => 'js:function( event, ui ){
						        var targetElem = event.target;
						        var elem = ui.draggable;

						        $(targetElem).append(elem.html());
						        elem.attr("style", "position:relative;");
						        }'
					    )
				    ));
			    echo $attribute->getTitle();
			    $this->endWidget();
		    }
		    echo '</ul>';
		    echo '</div>';
	    } ?>
    </div>
</div>