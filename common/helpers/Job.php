<?php

namespace common\helpers;

use Yii;

/**
 * Description of Job
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Job
{

    public static function sendEmail($data, $delay = 0)
    {
        return Yii::$app->queue->push('notive/send-mail', $data, $delay);
    }

    /**
     * @param array $ids registration_ids
     * @param array $data payload
     * ```php
     * [
     *     'notification' => [
     *         'title' => 'Title',
     *         'body' => "Message body",
     *     ],
     *     'data' => [
     *         'type' => 'POST_COMMENTED', // also other type
     *     ]
     * ]
     * ```
     * @param int $delay
     * @param int $retry
     */
    public static function fcmPush(array $ids, array $data, $delay = 0, $retry = null)
    {
        if (count($ids)) {
            Yii::$app->queue->push('notive/fcm-push', [$ids, $data, $retry], $delay);
        }
    }
}
