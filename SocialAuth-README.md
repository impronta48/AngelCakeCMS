Dirty hack to fix an issue with Admad Social Auth

if getting an error with `afterIdentify()` (less parameters than expected):

- open `vendor/admad/cakephp-social-auth/src/Middleware/SocialAuthMiddleware.php`
- replace line 283:
  - from `$event = $this->dispatchEvent(self::EVENT_AFTER_IDENTIFY, ['user' => $user]);`
  - to `$event = $this->dispatchEvent(self::EVENT_AFTER_IDENTIFY, ['user' => $user, 'response' => $response]);`
- replace line 290:
  - from `$user = $user->toArray();`
  - to `$user = $user['user']->toArray();` 