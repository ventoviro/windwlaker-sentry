<?php
/**
 * Part of phoenix project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Warder\Model;

use Phoenix\Model\CrudModel;
use Windwalker\Authentication\Credential;
use Windwalker\Core\User\User;
use Windwalker\Core\DateTime\DateTime;
use Windwalker\Core\Language\Translator;
use Windwalker\Core\Model\Exception\ValidateFailException;
use Windwalker\Data\Data;
use Lyrasoft\Warder\Helper\UserHelper;

/**
 * The LoginModel class.
 *
 * @since  {DEPLOY_VERSION}
 */
class UserModel extends \Lyrasoft\Warder\Admin\Model\UserModel
{
	/**
	 * register
	 *
	 * @param Data $user
	 *
	 * @return  bool
	 *
	 * @throws \Exception
	 */
	public function register(Data $user)
	{
		if ($user->password)
		{
			$user->password = UserHelper::hashPassword($user->password);
		}

		$this->prepareDefaultData($user);

		$user->id = User::save($user)->id;

		return true;
	}

	/**
	 * prepareDefaultData
	 *
	 * @param Data $user
	 *
	 * @return  void
	 */
	protected function prepareDefaultData(Data $user)
	{
		$user->registered = $user->registered ? : DateTime::create()->format(DateTime::FORMAT_SQL);
		$user->blocked = $user->blocked === null ? 1 : $user->blocked;
	}
}
