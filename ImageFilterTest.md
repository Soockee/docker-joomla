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
