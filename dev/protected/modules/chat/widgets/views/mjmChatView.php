<?php
/**
 * @var $showChat boolean
 */
?>

<div id="mjmChatRoom"<?php echo($showChat ? ' class="chatUp"' : '') ?>>
    <div class="chatWrapper">
        <div id="mjmChatRoomBody">
            <div id="chatMessages"></div>
            <ul id="mjmChatUsersList"></ul>
        </div>
        <div id="mjmChatRoomSend">
            <textarea id="mjmChatMessage"
                      placeholder="<?php echo Yii::t('chatModule.common', 'Введите сообщение') ?>"></textarea>
            <button id="mjmChatSend"
                    class="btn btn-primary"><?php echo Yii::t('chatModule.common', 'Отправить сообщение') ?></button>
        </div>
    </div>
    <div id="mjmChatRoomTitle"><span><?php echo Yii::t('chatModule.common', 'Чат (кликните, чтобы открыть)') ?></span></div>
</div>