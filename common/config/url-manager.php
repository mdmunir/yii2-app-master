<?php

function urlManagerFunc(UrlManager $urlManager)
{
    if ($urlManager->baseUrl == '') {
        $config = [
            'baseUrl' => '',
            'scriptUrl' => 'index.php',
            'hostInfo' => strtr($urlManager->hostInfo, ['http://' => 'http://blog.', 'https://' => 'https://blog.']),
        ];
    } else {
        $config = [
            'baseUrl' => strtr($urlManager->baseUrl, ['backend' => 'frontend']),
            'scriptUrl' => strtr($urlManager->scriptUrl, ['backend' => 'frontend']),
        ];
    }
    $config = array_merge($config, [
        'rules' => require Yii::getAlias('@frontend/config/url-rules.php'),
        'enablePrettyUrl' => true,
        'showScriptName' => $urlManager->showScriptName,
        ], Yii::$app->params['frontend.url.config']);

    return new UrlManager($config);
}
