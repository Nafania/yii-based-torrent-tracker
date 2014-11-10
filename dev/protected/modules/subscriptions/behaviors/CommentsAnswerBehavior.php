<?php
namespace modules\subscriptions\behaviors;

use Event;
use Yii;
use ChangesInterface;

/**
 * Class CommentsAnswerBehavior
 *
 * @method \Comment getOwner()
 */
class CommentsAnswerBehavior extends BaseEventBehavior
{
    public function afterSave($e)
    {
        parent::afterSave($e);

        $owner = $this->getOwner();
        $className = $owner->resolveClassName();

        if (!($owner instanceof ChangesInterface)) {
            return true;
        }

        if (!$owner->getIsNewRecord() || !$owner->getParentId()) {
            return true;
        }

        $ancestors = $owner->ancestors(10)->findAll();

        $users = array();
        //TODO not all ancestors return
        foreach ($ancestors AS $comment) {
            if ($user = $comment->user) {
                $users[] = $user;
            }
        }

        $users = array_unique($users);

        $data = [];

        foreach ($users AS $user) {
            if ($user->getId() == Yii::app()->getUser()->getId()) {
                continue;
            }

            $url = $owner->getUrl();
            $icon = $owner->getChangesIcon();

            $data[] = [

                'text' => $owner->getChangesText(),
                'title' => $owner->getChangesTitle(),
                'url' => $url,
                'icon' => $icon,
                'uId' => $user->getId(),
            ];
        }

        $this->saveEvent($data);
    }
}