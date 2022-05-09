<?php
namespace App\Event;

use ADmad\SocialAuth\Middleware\SocialAuthMiddleware;
use Authentication\Authenticator\CookieAuthenticator;
use Authentication\PasswordHasher\PasswordHasherTrait;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use Cake\Http\Client\Response;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Cookie\CookieInterface;
use Cake\Http\Response as HttpResponse;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Security;
use InvalidArgumentException;

class SocialAuthListener implements EventListenerInterface
{
    use LocatorAwareTrait;
    use PasswordHasherTrait;

    public function implementedEvents(): array
    {
        return [
            SocialAuthMiddleware::EVENT_AFTER_IDENTIFY => 'afterIdentify',
            // SocialAuthMiddleware::EVENT_BEFORE_REDIRECT => 'beforeRedirect',
            // Uncomment below if you want to use the event listener to return
            // an entity for a new user instead of directly using `createUser()` table method.
            // SocialAuthMiddleware::EVENT_CREATE_USER => 'createUser',
        ];
    }

    public function afterIdentify(EventInterface $event, EntityInterface $user, HttpResponse $response): Array
    {
        $users = $this->getTableLocator()->get('Users');
        $identity = $users->find()->where(['id'=>$user->id])->first();
        $value = $this->_createToken($identity);
        $cookie = $this->_createCookie('CookieAuth', $value);
        $response = $response->withCookie($cookie);

        return ['user'=> $user, 'response' => $response];
    }

    /**
     * Creates a full cookie token serialized as a JSON sting.
     *
     * Cookie token consists of a username and hashed username + password hash.
     *
     * @param array|\ArrayAccess $identity Identity data.
     * @return string
     */
    protected function _createToken($identity): string
    {
        $plain = $this->_createPlainToken($identity);
        $hash = $this->getPasswordHasher()->hash($plain);

        return json_encode([$identity->username, $hash]);
    }

    /**
     * Creates a cookie instance with configured defaults.
     *
     * @param mixed $value Cookie value.
     * @return \Cake\Http\Cookie\CookieInterface
     */
    protected function _createCookie($name, $value): CookieInterface
    {        
       $options = [
            'expires' => '2 weeks',
            'http' => true,
            'secure' => false,
       ];
       
        $cookie = Cookie::create(
            $name,
            $value,
            $options
        );

        return $cookie;
    }

    /**
     * Creates a plain part of a cookie token.
     *
     * Returns concatenated username, password hash, and HMAC signature.
     *
     * @param array|\ArrayAccess $identity Identity data.
     * @return string
     */
    protected function _createPlainToken($identity): string
    {
        $usernameField = 'username';
        $passwordField = 'password';

        $salt = Security::getSalt();
        $value = $identity[$usernameField] . $identity[$passwordField];

        if ($salt === false) {
            return $value;
        }
        if (!is_string($salt) || $salt === '') {
            throw new InvalidArgumentException('Salt must be a non-empty string.');
        }

        $hmac = hash_hmac('sha1', $value, $salt);
        // Instead of appending the plain salt, we create a hash. This limits the chance of the salt being leaked.

        return $value . $hmac;
    }

}
