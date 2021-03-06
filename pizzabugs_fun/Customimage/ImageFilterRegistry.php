<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Customimage;

\defined('JPATH_PLATFORM') or die;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\CMS\Customimage\CustomImageFilterRegistry as Registry;
use Joomla\CMS\Image\ImageFilterRegistry as defaultIFR;
/**
 * Service provider for the HTML service registry
 *
 * @since  __DEPLOY_VERSION__
 */
class ImageFilterRegistry implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	
	public function register(Container $container)
	{
		$container->share(
			Registry::class,
			function (Container $container)
			{
				return new Registry;
			},
			false
		);
	}
}
