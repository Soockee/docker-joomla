<?php
/**
 * @package     Joomla.Plugin
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

 namespace IFT;
defined('_JEXEC') or die;

// namespace Joomla\CMS\ImageFilterTest;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Image\ImageFilterRegistry;
use Joomla\CMS\Image\Filter\Brightness;
use Joomla\CMS\Factory;

/**
 * Joomla! Language Filter Plugin.
 *
 * @since  1.6
 */
class PlgSystemImageFilterTest extends CMSPlugin
{


	/**
	 * The service registry for Image Filters
	 *
	 * @var    ImageFilterRegistry
	 * @since  __DEPLOY_VERSION__
	 */


	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		Log::add('IFR Construct', Log::DEBUG, 'IFR');
	}
	/**
	 * After initialise.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function onAfterInitialise()
	{	
		Log::add('IFR STARTING  INIT', Log::DEBUG, 'IFR');
		$this->overwriteServiceRegistry();
		// $type = "brightness";
		$this->checkDefaultImageClass("brightness");
		$this->checkCustomImageClass("superbrightness");
	}
	
	public  function checkDefaultImageClass($type)
	{
		$image = new Image(imagecreatetruecolor(1, 1));
		// Verify that the filter type exists.
		$serviceRegistry = Image::getServiceRegistry();
		$className = $this->getClassName($type, $serviceRegistry);
		// Instantiate the filter object.
		$instance = new $className($image->getHandle());

		if(!$this->isValid($instance)){
			throw new \RuntimeException('The ' . ucfirst($type) . ' image filter is not valid.');
		}

	}
	public  function checkCustomImageClass($type)
	{
		$image = new CustomImages\CustomImage(imagecreatetruecolor(1, 1));
		// Verify that the filter type exists.
		$serviceRegistry =  CustomImages\CustomImage::getServiceRegistry();
		$className = $this->getClassName($type, $serviceRegistry);

		// Instantiate the filter object.
		$instance = new $className($image->getHandle());

		if(!isValid($instance)){
			throw new \RuntimeException('The ' . ucfirst($type) . ' image filter is not valid.');
		}
	}

	/**
	 *	get the classname by type
	 *
	 */
	public function getClassName($type, $serviceRegistry)
	{
		if ($serviceRegistry->hasService($type))
		{
			$className =  CustomImages\CustomImage::getServiceRegistry()->getService($type);
		}
		else{
			throw new \RuntimeException('The ' . $serviceRegistry . ' has no service of type ' . $type);
		}
		if(!$className){
			throw new \RuntimeException('The ' . ucfirst($className) . ' className is null.');
		}
		return $className;
	}


	/**
	 *	checks if a given instance is valid
	 *
	 */
	public function isValid($instance)
	{
		// Verify that the filter type is valid.
		if (!($instance instanceof Brightness))
		{
			throw new \RuntimeException('The ' . ucfirst($type) . ' image filter is not valid.');
		}
		Log::add('Verified that the filter is valid', Log::DEBUG, 'IFR');
		return TRUE;
	}

	/**
	 * overrides the service registry, which registered an additional filter.
	 *
	 */
	public function overwriteServiceRegistry()
	{
		$serviceRegistry = Factory::getContainer()->get(ImageFilterRegistry::class);
		$serviceRegistry->register("superbrightness", Filter\SuperBrightness::class);
		Factory::getContainer()->set(ImageFilterRegistry::class, $registry);
	}
}
