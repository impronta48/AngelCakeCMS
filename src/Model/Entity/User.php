<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Firebase\JWT\JWT;
use Cake\Utility\Security;

/**
 * User Entity
 *
 * @property string $id
 * @property string $username
 * @property string|null $email
 * @property string $password
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $token
 * @property \Cake\I18n\FrozenTime|null $token_expires
 * @property string|null $api_token
 * @property \Cake\I18n\FrozenTime|null $activation_date
 * @property string|null $secret
 * @property bool|null $secret_verified
 * @property \Cake\I18n\FrozenTime|null $tos_date
 * @property bool $active
 * @property bool $is_superuser
 * @property string|null $role
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string|null $additional_data
 *
 * @property \App\Model\Entity\Article[] $articles
 * @property \App\Model\Entity\Event[] $events
 * @property \App\Model\Entity\SocialAccount[] $social_accounts
 */
class User extends Entity
{
  /**
   * Fields that can be mass assigned using newEntity() or patchEntity().
   *
   * Note that when '*' is set to true, this allows all unspecified fields to
   * be mass assigned. For security purposes, it is advised to set '*' to false
   * (or remove it), and explicitly make individual fields accessible as needed.
   *
   * @var array
   */
  protected $_accessible = [
    '*' => true,
  ];

  /**
   * Fields that are excluded from JSON versions of the entity.
   *
   * @var array
   */
  protected $_hidden = [
    'password',
    'token',
  ];

  // Automatically hash passwords when they are changed.
  protected function _setPassword(string $password)
  {
    $hasher = new DefaultPasswordHasher();
    return $hasher->hash($password);
  }
  /**
     * Generate a refresh token valid for 30 days
     * @param int $uid User ID
     * @return string JWT refresh token
     */
    public function getRefreshToken($uid)
    {
        $expireTime = time() + self::REFRESH_TOKEN_MONTH_LIVE;
        $alg = 'HS256';
        $token = JWT::encode([
            'sub' => $uid,
            'exp' => $expireTime,
            'type' => 'refresh'
        ], Security::getSalt(), $alg);

        return $token;
    }

    /**
     * Validate and decode a refresh token
     * @param string $token JWT refresh token
     * @return object|null Decoded token data or null if invalid
     */
    public static function validateRefreshToken($token)
    {
        try {
            $decoded = JWT::decode($token, new \Firebase\JWT\Key(Security::getSalt(), 'HS256'));

            // Verify it's a refresh token
            if (!isset($decoded->type) || $decoded->type !== 'refresh') {
                return null;
            }

            // Verify token hasn't expired
            if ($decoded->exp < time()) {
                return null;
            }

            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public const TOKEN_HOUR_LIVE = 3600; // 1 hour
    public const DAY = 86400; // seconds in a day
    public const REFRESH_TOKEN_MONTH_LIVE = self::DAY * 30;

  public function getToken($uid, $duration = self::TOKEN_HOUR_LIVE)
    {
        $expireTime = time() + $duration;
        $alg = 'HS256'; // Replace with your algorithm
        $token = JWT::encode([
            'sub' => $uid,
            'exp' => $expireTime,
        ], Security::getSalt(), $alg);

        return $token;
    }

}
