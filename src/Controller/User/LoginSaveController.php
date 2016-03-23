<?php
/**
 * Part of Front project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Warder\Controller\User;

use Phoenix\Controller\AbstractSaveController;
use Windwalker\Data\Data;
use Windwalker\Warder\Helper\UserHelper;
use Windwalker\Warder\Helper\WarderHelper;
use Windwalker\Warder\Model\UserModel;

/**
 * The SaveController class.
 *
 * @since  1.0
 */
class LoginSaveController extends AbstractSaveController
{
	/**
	 * Property model.
	 *
	 * @var  UserModel
	 */
	protected $model;

	/**
	 * Property formControl.
	 *
	 * @var  string
	 */
	protected $formControl = 'user';

	/**
	 * Property langPrefix.
	 *
	 * @var  string
	 */
	protected $langPrefix = 'warder.login.';

	/**
	 * prepareExecute
	 *
	 * @return  void
	 */
	protected function prepareExecute()
	{
		if (UserHelper::isLogin())
		{
			$this->redirect($this->getSuccessRedirect());

			return;
		}

		parent::prepareExecute();
	}

	/**
	 * doSave
	 *
	 * @param Data $data
	 *
	 * @return void
	 */
	protected function doSave(Data $data)
	{
		$options   = array();
		$provider  = $this->input->get('provider');
		$loginName = WarderHelper::getLoginName();

		if ($provider)
		{
			$options['provider'] = strtolower($provider);

			$data->$loginName = null;
			$data->password = null;
		}

		$this->model->login($data->$loginName, $data->password, $data->remember, $options);
	}

	/**
	 * getSuccessRedirect
	 *
	 * @param Data $data
	 *
	 * @return  string
	 */
	protected function getSuccessRedirect(Data $data = null)
	{
		$return = $this->getUserState($this->getContext('return'));

		if ($return)
		{
			$this->removeUserState($this->getContext('return'));

			return base64_decode($return);
		}
		else
		{
			return $this->router->http(WarderHelper::getPackage()->get('frontend.redirect.login', 'home'));
		}
	}

	/**
	 * getFailRedirect
	 *
	 * @param Data $data
	 *
	 * @return  string
	 */
	protected function getFailRedirect(Data $data = null)
	{
		return $this->router->http('login');
	}
}
