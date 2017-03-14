<?php

namespace yii\helpers;

use Yii;

/**
 * Description of Url
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Url extends BaseUrl
{
    public static $app;
    public static $apps = [
        'backend',
        'frontend',
        'rest'
    ];

    /**
     * @inheritdoc
     */
    public static function toRoute($route, $scheme = false)
    {
        $route = (array) $route;
        $current = static::$app;
        foreach (static::$apps as $app) {
            if (strpos($route[0], "@{$app}/") === 0) {
                $route[0] = substr($route[0], strlen($app) + 1);
                static::$app = $app;
                if($scheme === false){
                    $scheme = true;
                }
            }
        }
        try {
            $url = parent::toRoute($route, $scheme);
            static::$app = $current;
            return $url;
        } catch (\Exception $exc) {
            static::$app = $current;
            throw $exc;
        }
    }

    /**
     * @inheritdoc
     */
    protected static function getUrlManager()
    {
        if (static::$app !== null && ($manager = Yii::$app->get(static::$app . 'UrlManager', false)) !== null) {
            return $manager;
        }
        return parent::getUrlManager();
    }
}
