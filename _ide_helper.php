<?php

exit("This file should not be included, only analyzed by your IDE");

namespace yii\web {

    /**
     * @property \yii\authclient\Collection $authClientCollection
     * @property \common\classes\Jwt $jwt
     * @property \common\classes\User $user
     * @property \dee\queue\Queue $queue
     * @property \common\classes\Formatter $formatter
     * @property \common\classes\Notification $notification
     * @property \common\classes\Firebase $firebase
     */
    class Application extends \yii\base\Application
    {
        public function handleRequest($request)
        {

        }
    }

}
