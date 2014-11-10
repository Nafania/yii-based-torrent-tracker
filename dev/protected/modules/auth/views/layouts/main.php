<?php /* @var $this AuthController */ ?>

<?php $this->beginContent($this->module->defaultLayout); ?>

<div class="auth-module">

	<h1>&nbsp;</h1>

	<?php $this->widget('zii.widgets.CMenu', array(
		//'type' => 'tabs',
		'htmlOptions' => array(
			'class' => 'tools',
		),
		'items' => array(
			array(
				'label' => Yii::t('AuthModule.main', 'Assignments'),
				'url' => array('/auth/assignment/index'),
				'active' => $this instanceof AssignmentController,
			    'linkOptions' => array('class' => ( $this instanceof AssignmentController ? 'focus' : '' ) )
			),
			array(
				'label' => $this->capitalize($this->getItemTypeText(CAuthItem::TYPE_ROLE, true)),
				'url' => array('/auth/role/index'),
				'active' => $this instanceof RoleController,
				'linkOptions' => array('class' => ( $this instanceof RoleController ? 'focus' : '' ) )
			),
			array(
				'label' => $this->capitalize($this->getItemTypeText(CAuthItem::TYPE_TASK, true)),
				'url' => array('/auth/task/index'),
				'active' => $this instanceof TaskController,
				'linkOptions' => array('class' => ( $this instanceof TaskController ? 'focus' : '' ) )
			),
			array(
				'label' => $this->capitalize($this->getItemTypeText(CAuthItem::TYPE_OPERATION, true)),
				'url' => array('/auth/operation/index'),
				'active' => $this instanceof OperationController,
				'linkOptions' => array('class' => ( $this instanceof OperationController ? 'focus' : '' ) )
			),
		),
	));?>

	<?php echo $content; ?>

</div>

<?php $this->endContent(); ?>