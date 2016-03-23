<?php
/**
 * Part of eng4tw project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Warder\Controller\User;

use Phoenix\Controller\AbstractPhoenixController;
use Windwalker\Core\Authentication\User;
use Windwalker\Warder\Helper\WarderHelper;

/**
 * The GetController class.
 *
 * @since  {DEPLOY_VERSION}
 */
class LogoutSaveController extends AbstractPhoenixController
{
	/**
	 * doExecute
	 *
	 * @return  mixed
	 */
	protected function doExecute()
	{
		User::logout();

		$return = $this->input->getBase64(
			$this->package->get('frontend.login.return_key', 'return')
		);

		if ($return)
		{
			$this->setRedirect(base64_decode($return));

			return true;
		}

		$this->setRedirect($this->router->http(WarderHelper::getPackage()->get('frontend.redirect.logout', 'home')));

		return true;
	}
}
