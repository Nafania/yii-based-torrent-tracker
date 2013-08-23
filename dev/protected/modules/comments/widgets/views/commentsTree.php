<div class="commentsBlock">
<?php if ( sizeof($comments) > 0 ){ ?>

        <?php foreach ( $comments as $comment ) {
			$this->render('application.modules.comments.widgets.views._commentView', array('comment' => $comment));
		} ?>

	<?php } ?>
</div>
