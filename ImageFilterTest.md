# Notes about ImageFilterTest

## Plugin Installation with Basic logging

```php
<?php
/**
 * @package     Joomla.Plugin
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Log\Log;

/**
 * Joomla! Language Filter Plugin.
 *
 * @since  1.6
 */
class PlgSystemImageFilterTest extends CMSPlugin
{

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
		Log::add('IFR AFTER INIT', Log::DEBUG, 'IFR');
	}
}

```

Plugin XML

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="plugin" group="system" method="upgrade">
	<name>plg_system_imagefiltertest</name>
	<author>Joomla! Project</author>
	<creationDate>July 2021</creationDate>
	<copyright>(C) 2021 Open Source Matters, Inc.</copyright>
	<license>GNU General Public License version 2 or later; see LICENSE.txt</license>
	<authorEmail>admin@joomla.org</authorEmail>
	<authorUrl>www.joomla.org</authorUrl>
	<version>4.0.0</version>
	<description>PLG_SYSTEM_IMAGEFILTERTEST_DESCRIPTION</description>
	<files>
		<filename plugin="imagefiltertest">imagefiltertest.php</filename>
	</files>
</extension>
```

## Notes: 

does following snipped (90) in  ImageFilterRegistry.php work properly for namespaces with aliases?
```
if (\is_string($handler) && !class_exists($handler))
```

concern comes from : https://www.php.net/manual/en/function.class-exists.php
```
If you are using aliasing to import namespaced classes, take care that class_exists will not work using the short, aliased class name - apparently whenever a class name is used as string, only the full-namespace version can be used

use a\namespaced\classname as coolclass;

class_exists( 'coolclass' ) => false
```




## Second Approach 

System/imagefiltertest/imagefiltertest.php

```
<?php
/**
 * @package     Joomla.Plugin
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Image\ImageFilterRegistry;
use Joomla\CMS\Image\Filter\Brightness;
use Joomla\CMS\Factory;
use Joomla\CMS\Customimage\Filter\SuperBrightness;
use Joomla\CMS\Customimage\CustomImage;
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
		JLoader::import('socke.customimage.library');
		Log::add('IFR STARTING  INIT', Log::DEBUG, 'IFR');
	// $image = new CustomImage(imagecreatetruecolor(1, 1));
	// $image->getServiceRegistry()->register("superbrightness", SuperBrightness::class);
		Log::add('CUSTOM NAMESPACES IMAGE CREATED', Log::DEBUG, 'IFR');

		// $type = "brightness";
		$this->checkDefaultImageClassWithCustomFilter("superbrightness");
		$this->checkCustomImageClass("superbrightness");
	}
	
	public  function checkDefaultImageClassWithCustomFilter($type)
	{
		$image = new Image(imagecreatetruecolor(1, 1));
		// Verify that the filter type exists.
		$serviceRegistry = Image::getServiceRegistry();
		if(!$serviceRegistry->hasService($type)){
			$serviceRegistry->register($type, SuperBrightness::class);
		}
		$className = $this->getClassName($type, $serviceRegistry, Image::class);
		// Instantiate the filter object.
		$instance = new $className($image->getHandle());

		if(!$this->isValid($instance, SuperBrightness::class)){
			throw new \RuntimeException('The ' . ucfirst($type) . ' image filter is not valid.');
		}

	}
	public  function checkCustomImageClass($type)
	{
		$image = new CustomImage(imagecreatetruecolor(1, 1));
		// Verify that the filter type exists.
		$serviceRegistry =  CustomImage::getServiceRegistry();
		if(!$serviceRegistry->hasService($type)){
			$serviceRegistry->register($type, SuperBrightness::class);
		}
		$className = $this->getClassName($type, $serviceRegistry, CustomImage::class);

		// Instantiate the filter object.
		$instance = new $className($image->getHandle());

		if(!$this->isValid($instance, SuperBrightness::class)){
			throw new \RuntimeException('The ' . ucfirst($type) . ' image filter is not valid.');
		}
	}

	/**
	 *	get the classname by type
	 *
	 */
	public function getClassName($type, $serviceRegistry, $imageclass)
	{
		if ($serviceRegistry->hasService($type))
		{
			$className =  $imageclass::getServiceRegistry()->getService($type);
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
	public function isValid($instance, $instanceCheckType)
	{
		// Verify that the filter type is valid.
		if (!($instance instanceof $instanceCheckType))
		{
			throw new \RuntimeException('The ' . get_class($instance) . ' image filter with is not valid.');
		}
		Log::add('Verified that the ' . get_class($instance) . ' filter is of type ' . $instanceCheckType . ' and therefore valid', Log::DEBUG, 'IFR');
		return TRUE;
	}
}

```


Libraries/src/CustomImage uses different namespace then image  Libraries/src/Image



https://github.com/joomla/joomla-cms/pull/31818