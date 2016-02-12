<?php
/**
 * Part of phoenix project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Warder\Admin\Model;

use Phoenix\Model\AdminModel;
use Windwalker\Authentication\Credential;
use Windwalker\Core\Authentication\User;
use Windwalker\Core\DateTime\DateTime;
use Windwalker\Core\Language\Translator;
use Windwalker\Core\Model\Exception\ValidFailException;
use Windwalker\Crypt\Password;
use Windwalker\Data\Data;
use Windwalker\Record\Record;
use Windwalker\Warder\Helper\UserHelper;

/**
 * The UserModel class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class UserModel extends AdminModel
{
	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = 'user';

	/**
	 * Property reorderConditions.
	 *
	 * @var  array
	 */
	protected $reorderConditions = array();

	/**
	 * getItem
	 *
	 * @param   mixed $pk
	 *
	 * @return  Data
	 */
	public function getItem($pk = null)
	{
		$state = $this->state;

		$pk = $pk ? : $state['item.pk'];

		return $this->fetch('item.' . json_encode($pk), function() use ($pk, $state)
		{
			$item = User::get($pk);

			$item = new Data($item->dump());

			$this->postGetItem($item);

			return $item;
		});
	}

	/**
	 * login
	 *
	 * @param string $username
	 * @param string $password
	 * @param bool   $remember
	 * @param array  $options
	 *
	 * @return bool
	 * @throws ValidFailException
	 */
	public function login($username, $password, $remember = false, $options = array())
	{
		$credential = new Credential;
		$credential->username = $username;
		$credential->password = $password;

		$result = User::login($credential, (bool) $remember, $options);

		if (!$result)
		{
			throw new ValidFailException(Translator::translate('warder.login.message.fail'));
		}

		return $result;
	}

	/**
	 * save
	 *
	 * @param Data $user
	 *
	 * @return bool
	 * @throws ValidFailException
	 */
	public function save(Data $user)
	{
		if ('' !== (string) $user->password)
		{
			$user->password = UserHelper::hashPassword($user->password);
		}

		unset($user->password2);

		$user = User::save($user);

		User::getHandler()->login($user);

		return true;
	}

	/**
	 * getDefaultData
	 *
	 * @return array
	 */
	public function getDefaultData()
	{
		$item = parent::getDefaultData();

		unset($item['password']);
		unset($item['password2']);

		return $item;
	}
}
