<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Lyrasoft\Warder;

use Lyrasoft\Unidev\Helper\PravatarHelper;
use Lyrasoft\Warder\Admin\DataMapper\UserMapper;
use Lyrasoft\Warder\Data\WarderUserDataInterface;
use Lyrasoft\Warder\Helper\AvatarUploadHelper;
use Lyrasoft\Warder\Helper\WarderHelper;
use function Windwalker\arr;
use Windwalker\Core\Ioc;
use Windwalker\Core\Security\Hasher;
use Windwalker\Core\User\User;
use Windwalker\Crypt\CryptHelper;
use Windwalker\Crypt\Password;

/**
 * The Warder class.
 *
 * @method static WarderUserDataInterface getUser($conditions = [])
 * @method static WarderUserDataInterface get($conditions = [])
 * @method static WarderUserDataInterface save($user = [], $options = [])
 *
 * @since  1.4.2
 */
class Warder extends User
{
    /**
     * isLogin
     *
     * @return  boolean
     */
    public static function isLogin()
    {
        $user = static::getUser();

        return $user->isMember();
    }

    /**
     * authorise
     *
     * @param bool $defaultRequireLogin
     *
     * @return  boolean
     */
    public static function requireLogin($defaultRequireLogin = true)
    {
        $config = static::getContainer()->get('config');

        $requestLogin = (bool) $config->get('route.extra.warder.require_login', $defaultRequireLogin);

        return $requestLogin !== false && !static::isLogin();
    }

    /**
     * hashPassword
     *
     * @param   string $password
     * @param int      $algo
     * @param array    $options
     *
     * @return  string
     */
    public static function hashPassword($password, $algo = PASSWORD_DEFAULT, array $options = [])
    {
        return Hasher::create($password, $algo, $options);
    }

    /**
     * verifyPassword
     *
     * @param   string $password
     * @param   string $hash
     *
     * @return  boolean
     */
    public static function verifyPassword($password, $hash)
    {
        return Hasher::verify($password, $hash);
    }

    /**
     * needsRehash
     *
     * @param string $password
     * @param int    $algo
     * @param array  $options
     *
     * @return  bool
     *
     * @since  1.5.2
     */
    public static function needsRehash($password, $algo = PASSWORD_DEFAULT, array $options = [])
    {
        return Hasher::needsRehash($password, $algo, $options);
    }

    /**
     * goToLogin
     *
     * @param   string $return
     *
     * @return  void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public static function goToLogin($return = null)
    {
        $query = [];

        if ($return) {
            $query['return'] = base64_encode($return);
        }

        $package = WarderHelper::getPackage()->getCurrentPackage();

        $url = $package->router->route('login', $query);

        Ioc::getApplication()->redirect($url);
    }

    /**
     * fakeAvatar
     *
     * @param int    $size
     * @param string $uniqid
     *
     * @return  string
     * @throws \Exception
     */
    public static function fakeAvatar($size = 300, $uniqid = null)
    {
        return PravatarHelper::unique($size, $uniqid);
    }

    /**
     * defaultAvatar
     *
     * @return  string
     */
    public static function defaultAvatar()
    {
        return AvatarUploadHelper::getDefaultImage();
    }

    /**
     * getToken
     *
     * @param string $data
     * @param string $secret
     *
     * @return  string
     */
    public static function getToken($data = null, $secret = null)
    {
        $secret = $secret ?: Ioc::getConfig()->get('system.secret');

        $data = json_encode($data);

        return md5($secret . $data . uniqid('Warder', true) . CryptHelper::genRandomBytes());
    }

    /**
     * getReceiveMailUsers
     *
     * @param array $conditions
     *
     * @return  \Windwalker\Data\Data[]|\Windwalker\Data\DataSet
     *
     * @since   1.4.2
     */
    public static function getReceiveMailUsers($conditions = [])
    {
        $conditions['receive_mail'] = 1;
        $conditions['group'] = arr((array) static::getWarderPackage()->get('groups'))
            ->apply(function (array $storage) {
                return array_filter($storage, static function ($group) {
                    return (bool) ($group['is_admin'] ?? false);
                });
            })
            ->keys()
            ->dump();

        return UserMapper::find($conditions);
    }

    /**
     * getGroups
     *
     * @return  array
     *
     * @since  1.7.3
     */
    public static function getGroups(): array
    {
        return (array) static::getWarderPackage()->get('groups');
    }

    /**
     * getWarderPackage
     *
     * @return  WarderPackage
     *
     * @since  1.7.3
     */
    public static function getWarderPackage(): WarderPackage
    {
        return WarderHelper::getPackage();
    }
}
