<?php

namespace common\classes;

use Yii;
use yii\base\UnknownPropertyException;
use common\models\Client;
use yii\web\Cookie;

/**
 * Description of User
 *
 * @property int $clientId
 * @property Client $client
 * @property string $token
 *
 * @property string $username Description
 * @property string $fullname Description
 * @property string $address Description
 * @property string $bio Description
 * @property string $gender Description
 * @property string $avatarUrl Description
 * @property string $email Description
 *
 * @property \app\models\ar\UserProfile $profile
 * @property \app\models\ar\Company $company
 * @property \common\models\User $identity
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class User extends \yii\web\User
{
    /**
     *
     * @var array
     */
    public $attributes = [];
    /**
     *
     * @var bool
     */
    public $useClientCookie = false;
    /**
     *
     * @var string
     */
    public $clientCookieKey = '__client_id';
    /**
     *
     * @var bool 
     */
    public $autoCreateCookieClient = true;
    /**
     *
     * @var int
     */
    private $_clientId;
    /**
     *
     * @var Client
     */
    private $_client;
    /**
     *
     * @var bool
     */
    private $_tryCookie = false;
    /**
     *
     * @var string
     */
    private $_token;

    /**
     *
     * @return int
     */
    public function getClientId()
    {
        if ($this->_clientId === null && $this->getUseCookie() && !$this->_tryCookie) {
            // try get from cokie
            $this->_clientId = Yii::$app->getRequest()->getCookies()->getValue($this->clientCookieKey);
            $this->_tryCookie = true;

            if ($this->_clientId) {
                $cookie = new Cookie([
                    'name' => $this->clientCookieKey,
                    'value' => $this->_clientId,
                    'expire' => time() + 365 * 24 * 3600,
                ]);
                Yii::$app->getResponse()->getCookies()->add($cookie);
            } elseif ($this->autoCreateCookieClient) {
                $this->registerClient();
            }
        }
        return $this->_clientId;
    }

    /**
     *
     */
    public function registerClient()
    {
        $client = new Client([
            'user_id' => $this->getId(),
        ]);
        if ($client->save()) {
            $this->_clientId = $client->id;
            $this->_client = $client;
            if ($this->getUseCookie()) {
                $cookie = new Cookie([
                    'name' => $this->clientCookieKey,
                    'value' => $this->_clientId,
                    'expire' => time() + 365 * 24 * 3600,
                ]);
                Yii::$app->getResponse()->getCookies()->add($cookie);
            }
            return $this->_clientId;
        }
    }

    /**
     *
     * @return Client
     */
    public function getClient()
    {
        if ($this->_client === null && ($clientId = $this->getClientId()) !== null) {
            $this->_client = Client::findOne($clientId);
        }
        return $this->_client;
    }

    /**
     * @inheritdoc
     */
    public function loginByAccessToken($token, $type = null)
    {
        try {
            $jwt = Yii::$app->jwt->decode($token);
            $this->_token = $token;
            $this->_clientId = $jwt->cid;
            $this->_client = null;
            $this->trigger('loginByAccessToken');
            if ($client = $this->getClient()) {
                if (($user = $client->user) && $this->login($user)) {
                    return $user;
                }
                return false;
            }
            return null;
        } catch (Exception $exc) {
            return null;
        }
    }

    /**
     *
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     *
     * @param name $name
     * @return mixed
     */
    public function getState($name)
    {
        if ($this->getClient()) {
            return $this->getClient()->get($name);
        }
    }

    /**
     *
     * @param name $name
     * @param mixed $value
     */
    public function setState($name, $value)
    {
        if ($this->getClient()) {
            $this->getClient()->set($name, $value);
        }
    }
    private $_profiles = [];

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (UnknownPropertyException $exc) {
            $identity = $this->getIdentity();
            if (array_key_exists($name, $this->_profiles)) {
                return $this->_profiles[$name];
            } elseif (in_array($name, $this->attributes, true)) {
                return $this->_profiles[$name] = $identity ? $identity->$name : null;
            }
            throw $exc;
        }
    }

    /**
     * @inheritdoc
     */
    protected function afterLogin($identity, $cookieBased, $duration)
    {
        if (($client = $this->getClient()) !== null) {
            $user_id = $identity->getId();
            if ($client->user_id == null) {
                $client->user_id = $user_id;
                $client->save();
            } elseif ($client->user_id != $user_id) {
                $this->registerClient();
            }
        }
        $this->_profiles = [];
        parent::afterLogin($identity, $cookieBased, $duration);
    }

    /**
     * @inheritdoc
     */
    protected function afterLogout($identity)
    {
        if (($client = $this->getClient()) !== null && $client->user_id !== null) {
            $client->user_id = null;
            $client->save();
            if ($this->getUseCookie()) {
                Yii::$app->getResponse()->getCookies()->remove($this->clientCookieKey);
            }
        }
        parent::afterLogout($identity);
    }

    protected function getUseCookie()
    {
        if ($this->useClientCookie === null) {
            return $this->enableSession;
        }
        return $this->useClientCookie;
    }
}
