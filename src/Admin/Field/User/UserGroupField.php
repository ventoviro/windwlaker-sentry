<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 .
 * @license    __LICENSE__
 */

namespace Lyrasoft\Warder\Admin\Field\User;

use Lyrasoft\Warder\Warder;
use Windwalker\Form\Field\ListField;
use Windwalker\Html\Option;

/**
 * The UserGroupField class.
 *
 * @since  __DEPLOY_VERSION__
 */
class UserGroupField extends ListField
{
    /**
     * prepareOptions
     *
     * @return  array|Option[]
     */
    protected function prepareOptions()
    {
        $options = [];

        foreach (Warder::getGroups() as $name => $group) {
            $options[] = new Option(
                __($group['title'] ?? $name),
                $name
            );
        }

        return $options;
    }
}
