<?php

namespace task\controllers;

use Yii;
use dee\queue\WorkerController;
use app\models\ar\FirebaseSubscribe;
use app\models\ar\User;
use app\models\ar\Client;
use common\helpers\Job;

/**
 * Description of FirebaseController
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class FirebaseController extends WorkerController
{
    const DELIMITER = '_DEE_';

    public function actionNewPost()
    {
        $post = $this->getActionParams();
        $key = $post['_key'];
        $user_id = $post['userId'];
        $command = Yii::$app->db->createCommand();
        $command->insert('firebase_post', [
            'key' => $key,
            'user_id' => $user_id,
        ])->execute();
        $command->insert('firebase_keys', [
            'key' => $key,
            'type' => 'POST',
        ])->execute();
        $command->update('firebase_state_key', [
                'last_post' => $key
                ], ['AND',
                ['id' => 1],
                ['<', 'last_post', $key]
            ])
            ->execute();
        // subscribe
        FirebaseSubscribe::subscribe('FIREBASE', $key, true, $user_id);
        if (isset($post['status']['content'])) {
            $this->notiveMention($post['status']['content'], 'post', $key, $user_id);
        }
    }

    public function actionNewComment()
    {
        $comment = $this->getActionParams();
        if (!isset($comment['objectKey'])) {
            return;
        }
        $key = $comment['_key'];
        $user_id = $comment['userId'];
        $command = Yii::$app->db->createCommand();
        $command->insert('firebase_comment', [
            'key' => $key,
            'user_id' => $user_id,
        ])->execute();
        $command->insert('firebase_keys', [
            'key' => $key,
            'type' => 'COMMENT',
        ])->execute();
        $command->update('firebase_state_key', [
                'last_comment' => $key
                ], ['AND',
                ['id' => 1],
                ['<', 'last_comment', $key]
            ])
            ->execute();

        // subscribe
        list($postKey, ) = explode(self::DELIMITER, $comment['objectKey'], 2);
        FirebaseSubscribe::subscribe('FIREBASE', $key, true, $user_id);
        FirebaseSubscribe::subscribe('FIREBASE', $postKey, false, $user_id);
        // send notive to follower
        FirebaseSubscribe::sendNotive('COMMENT', $postKey, $user_id);
        if (isset($comment['content'])) {
            $this->notiveMention($comment['content'], 'comment', $key, $user_id);
        }
    }

    public function actionNewLike()
    {
        $post = $this->getActionParams();
        list($postKey, $user_id) = explode(self::DELIMITER, $post['_key'], 2);
        $time = $post['val'];
        $command = Yii::$app->db->createCommand();

        $command->update('firebase_state_key', [
                'last_like' => $time
                ], ['AND',
                ['id' => 1],
                ['<', 'last_like', $time]
            ])
            ->execute();
        // send notive
        FirebaseSubscribe::sendNotive('LIKE', $postKey, $user_id);
    }

    protected function notiveMention($content, $type, $key, $user_id)
    {
        if (preg_match_all('/@(\w+)/', $content, $matches)) {
            $user = User::findOne($user_id);
            $name = $user ? $user->profile->fullname : '';
            $names = $matches[1];
            $clients = Client::find()->alias('c')
                ->joinWith('user u')
                ->where(['u.username' => $names])
                ->andWhere(['NOT', ['c.notive_key' => null]])
                ->andWhere(['<>', 'u.id', $user_id])
                ->all();
            if (count($clients)) {
                $ids = [];
                foreach ($clients as $client) {
                    $ids[] = $client->notive_key;
                }

                $data = [
                    'data' => [
                        'title' => 'Mention',
                        'body' => "You are mensioned by $name in their $type",
                        'type' => $type === 'post' ? 'POST_MENTIONED' : 'COMMENT_MENTIONED',
                        'key' => $key,
                        'object_id' => $key,
                    ]
                ];
                Job::fcmPush($ids, $data);
            }
        }
    }
}
