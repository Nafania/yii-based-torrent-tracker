<?php
/**
 * @var $showChat boolean
 */
?>

<div id="mjmChatRoom"<?php echo ( $showChat ? ' style="bottom:0;right:0;"' : '' ) ?>>
	<div id="mjmChatRoomHead">
		<span id="mjmChatRoomTitle" title="Minimize"><?php echo Yii::t('chatModule.common', 'Чат') ?></span>
		<span id="mjmChatRoomMinimize" title="Minimize">-</span>
	</div>
	<div id="mjmChatRoomBody">
		<div id="chatMessages"></div>
		<ul id="mjmChatUsersList"></ul>
	</div>
	<div id="mjmChatRoomSend">
		<textarea id="mjmChatMessage" placeholder="<?php echo Yii::t('chatModule.common', 'Введите сообщение') ?>"></textarea>
		<button id="mjmChatSend" class="btn btn-primary"><?php echo Yii::t('chatModule.common', 'Отправить сообщение') ?></button>
	</div>
</div>