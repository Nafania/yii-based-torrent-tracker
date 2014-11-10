<?php

/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 19.07.14
 * Time: 14:07
 */
class ConvertForumToBlogsCommand extends CConsoleCommand
{
    /**
     * @var CDbConnection
     */
    public $oldDb;

    /**
     * @var CDbConnection
     */
    public $newDb;

    /**
     * @var YiiDecoda
     */
    public $bbcodeParser;

    public function init()
    {
        if (!$this->oldDb) {
            if (!Yii::app()->getComponent('oldDb')) {
                throw new CException('Component oldDb not found, please specify oldDb connection in config');
            }

            $this->oldDb = Yii::app()->oldDb;
        }

        if (!$this->newDb) {
            $this->newDb = Yii::app()->db;
        }

        if (!$this->bbcodeParser) {
            $this->bbcodeParser = Yii::createComponent([
                'class' => 'ext.decoda.YiiDecoda',
                'defaults' => true,
                'disableHooks' => true,
            ]);
            $this->bbcodeParser->init();
            Yii::app()->setComponent('decoda', $this->bbcodeParser);
        }
    }

    public function actionIndex()
    {
    }

    public function actionConvertForumToGroupOrBlog($forumId, $blogId, $groupId = null)
    {
        $topicsInfo = [];
        $rows = $this->oldDb->createCommand('SELECT topic_id, topic_title, topic_type, topic_time FROM forum_topics WHERE topic_status != 1 AND forum_id = :forumId ORDER BY topic_time ASC')->queryAll(true, [':forumId' => $forumId]);

        foreach ($rows AS $row) {
            $topicsInfo[$row['topic_id']] = $row;
        }

        $rows = $this->oldDb->createCommand('SELECT p.post_time, pt.post_text, pt.post_subject, u.email, p.topic_id FROM forum_posts p LEFT JOIN users u ON (p.poster_id = u.uid), forum_posts_text pt WHERE p.post_id = pt.post_id AND p.forum_id = :forum_id AND p.topic_id IN (' . implode(', ', array_keys($topicsInfo)) . ') ORDER BY p.post_time ASC')->queryAll(true, [':forum_id' => $forumId]);

        $emails = [];
        foreach ($rows AS $row) {
            $emails[] = $row['email'];

            $row['post_text'] = preg_replace('/\[spoiler=&quot;(.*?)&quot;\](.*?)\[\/spoiler\]/si', '[note]\\1[/note][note]\\2[/note]', $row['post_text']);
            $row['post_text'] = preg_replace('/\[spoiler\](.*?)\[\/spoiler\]/si', '[note]\\1[/note]', $row['post_text']);
            $row['post_text'] = preg_replace('/\[spoiler=\'(.*?)\'\](.*?)\[\/spoiler\]/si', '[note]\\1[/note][note]\\2[/note]', $row['post_text']);
            $row['post_text'] = str_replace('&nbsp;', ' ', $this->bbcodeParser->parse($row['post_text']));

            $topicsInfo[$row['topic_id']]['posts'][] = $row;
        }

        $newUsersData = $this->getUsersByEmail($emails);

        $transaction = $this->newDb->beginTransaction();

        foreach ($newUsersData AS $email => $id) {
            if ($groupId !== null) {
                $this->newDb->createCommand('INSERT IGNORE INTO groupUsers (idGroup, idUser, ctime, status) VALUES(:idGroup, :idUser, :ctime, :status)')->execute([
                    ':idGroup' => $groupId,
                    ':idUser' => $id,
                    ':ctime' => time(),
                    ':status' => GroupUser::STATUS_APPROVED,
                ]);
            }

            $this->newDb->createCommand('INSERT IGNORE INTO subscriptions (modelId, modelName, uId, ctime) VALUES(:modelId, :modelName, :uId, :ctime)')->execute([
                ':modelId' => ($groupId !== null ? $groupId : $blogId),
                ':uId' => $id,
                ':ctime' => time(),
                ':modelName' => ($groupId !== null ? 'Group' : 'modules_blogs_models_Blog'),
            ]);
        }

        foreach ($topicsInfo AS $topicId => $data) {

            $postId = 0;

            if ( !isset($data['posts']) ) {
                continue;
            }

            foreach ($data['posts'] AS $i => $post) {

                if ($i == 0) {

                    $this->newDb->createCommand('INSERT INTO blogPosts (title, text, blogId, ownerId, ctime, mtime, hidden, pinned) VALUES(:title, :text, :blogId, :ownerId, :ctime, :mtime, :hidden, :pinned)')->execute([
                        ':title' => html_entity_decode($data['topic_title']),
                        ':text' => $post['post_text'],
                        ':blogId' => $blogId,
                        ':ownerId' => (!empty($newUsersData[$post['email']]) ? $newUsersData[$post['email']] : null),
                        ':ctime' => $post['post_time'],
                        ':mtime' => 0,
                        ':hidden' => 0,
                        ':pinned' => $topicsInfo['topic_type'] != 0 ? 1 : 0
                    ]);

                    $postId = $this->newDb->getLastInsertID();
                }
                else {
                    $this->newDb->createCommand('INSERT INTO comments (text, ownerId, ctime, modelName, modelId) VALUES(:text, :ownerId, :ctime, :modelName, :modelId)')->execute([
                        ':text' => $post['post_text'],
                        ':ownerId' => (!empty($newUsersData[$post['email']]) ? $newUsersData[$post['email']] : null),
                        ':ctime' => $post['post_time'],
                        ':modelName' => 'modules_blogs_models_BlogPost',
                        ':modelId' => $postId,
                    ]);
                }

                if ( $uId = $newUsersData[$post['email']] ) {
                    $this->newDb->createCommand('INSERT IGNORE INTO subscriptions (modelId, modelName, uId, ctime) VALUES(:modelId, :modelName, :uId, :ctime)')->execute([
                        ':modelId' => $postId,
                        ':uId' => $uId,
                        ':ctime' => time(),
                        ':modelName' => 'modules_blogs_models_BlogPost',
                    ]);
                }
            }

            $this->newDb->createCommand('INSERT INTO commentCounts (modelName, modelId, `count`) VALUES(:modelName, :modelId, :count)')->execute([
                ':count' => sizeof($data['posts']) - 1,
                ':modelName' => 'modules_blogs_models_BlogPost',
                ':modelId' => $postId,
            ]);
        }

        $transaction->commit();

    }

    public function actionConvertPhotoTopicToBlogPosts($topicId, $blogId, $groupId)
    {
        $topicInfo = $this->oldDb->createCommand('SELECT topic_title FROM forum_topics WHERE topic_id = :topicId')->queryRow(true, [':topicId' => $topicId]);

        $oldPosts = $this->oldDb->createCommand('SELECT p.post_time, pt.post_text, pt.post_subject, u.email FROM forum_posts p LEFT JOIN users u ON (p.poster_id = u.uid), forum_posts_text pt WHERE p.post_id = pt.post_id AND p.topic_id = :topic_id ORDER BY p.post_time ASC')->queryAll(true, [':topic_id' => $topicId]);

        $newData = [];
        $emails = [];

        foreach ($oldPosts AS $i => $post) {
            $emails[] = $post['email'];

            $post['post_text'] = preg_replace('/\[spoiler=&quot;(.*?)&quot;\](.*?)\[\/spoiler\]/si', '[note]\\1[/note][note]\\2[/note]', $post['post_text']);
            $post['post_text'] = preg_replace('/\[spoiler\](.*?)\[\/spoiler\]/si', '[note]\\1[/note]', $post['post_text']);
            $post['post_text'] = preg_replace('/\[spoiler=\'(.*?)\'\](.*?)\[\/spoiler\]/si', '[note]\\1[/note][note]\\2[/note]', $post['post_text']);

            if (stripos($post['post_text'], '[img') !== false) {
                $post['post_text'] = $this->bbcodeParser->parse($post['post_text']);
                $postId = $i;
                $newData[$postId] = $post;
            } else {
                $post['post_text'] = $this->bbcodeParser->parse($post['post_text']);
                $newData[$postId]['comments'][] = $post;
            }
        }

        $newUsersData = $this->getUsersByEmail($emails);

        $transaction = $this->newDb->beginTransaction();

        //$this->newDb->createCommand('DELETE FROM groupUsers WHERE idGroup = :idGroup')->execute([':idGroup' => $groupId]);
        //$this->newDb->createCommand('DELETE FROM subscriptions WHERE modelId = :modelId AND modelName = \'Group\'')->execute([':modelId' => $groupId]);

        foreach ($newUsersData AS $email => $id) {
            $this->newDb->createCommand('INSERT IGNORE INTO groupUsers (idGroup, idUser, ctime, status) VALUES(:idGroup, :idUser, :ctime, :status)')->execute([
                ':idGroup' => $groupId,
                ':idUser' => $id,
                ':ctime' => time(),
                ':status' => GroupUser::STATUS_APPROVED,
            ]);

            $this->newDb->createCommand('INSERT IGNORE INTO subscriptions (modelId, modelName, uId, ctime) VALUES(:modelId, :modelName, :uId, :ctime)')->execute([
                ':modelId' => $groupId,
                ':uId' => $id,
                ':ctime' => time(),
                ':modelName' => 'Group',
            ]);
        }

        //$this->newDb->createCommand('DELETE FROM blogPosts WHERE blogId = :blogid')->execute([':blogid' => $blogId]);

        foreach ($newData AS $i => $data) {
            $this->newDb->createCommand('INSERT INTO blogPosts (title, text, blogId, ownerId, ctime, mtime, hidden, pinned) VALUES(:title, :text, :blogId, :ownerId, :ctime, :mtime, :hidden, :pinned)')->execute([
                ':title' => ($data['post_subject'] ? $data['post_subject'] : $topicInfo['topic_title'] . ' ' . $i),
                ':text' => $data['post_text'],
                ':blogId' => $blogId,
                ':ownerId' => (!empty($newUsersData[$data['email']]) ? $newUsersData[$data['email']] : null),
                ':ctime' => $data['post_time'],
                ':mtime' => 0,
                ':hidden' => 0,
                ':pinned' => 0
            ]);

            $postId = $this->newDb->getLastInsertID();

            if (isset($data['comments'])) {
                foreach ($data['comments'] AS $comment) {
                    $this->newDb->createCommand('INSERT INTO comments (text, ownerId, ctime, modelName, modelId) VALUES(:text, :ownerId, :ctime, :modelName, :modelId)')->execute([
                        ':text' => $comment['post_text'],
                        ':ownerId' => (!empty($newUsersData[$comment['email']]) ? $newUsersData[$comment['email']] : null),
                        ':ctime' => $comment['post_time'],
                        ':modelName' => 'modules_blogs_models_BlogPost',
                        ':modelId' => $postId,
                    ]);
                }

                $this->newDb->createCommand('INSERT INTO commentCounts (modelName, modelId, `count`) VALUES(:modelName, :modelId, :count)')->execute([
                    ':count' => sizeof($data['comments']),
                    ':modelName' => 'modules_blogs_models_BlogPost',
                    ':modelId' => $postId,
                ]);
            }
        }

        $transaction->commit();
    }

    protected function getUsersByEmail(array $emails)
    {
        $emails = array_unique(array_filter($emails));

        $inQuery = implode(',', array_fill(0, count($emails), '?'));

        $command = $this->newDb->createCommand('SELECT id, email FROM users WHERE email IN(' . $inQuery . ')');

        $i = 1;
        foreach ($emails AS $email) {
            $command->bindValue($i, $email);
            ++$i;
        }

        $ret = [];

        foreach ($command->queryAll() AS $val) {
            $ret[$val['email']] = $val['id'];
        }

        return $ret;
    }
}