<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Image
 *
 * @copyright   (C) 2005 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace   Joomla\CMS\Customimage\Filter;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Image\ImageFilter;
use Joomla\CMS\Log\Log;

/**
 * Image Filter class adjust the brightness of an image.
 *
 * @since  2.5.0
 */
class SuperBrightness extends ImageFilter
{
	/**
	 * Method to apply a filter to an image resource.
	 *
	 * @param   array  $options  An array of options for the filter.
	 *
	 * @return  void
	 *
	 * @since   2.5.0
	 * @throws  \InvalidArgumentException
	 */
	public function execute(array $options = [])
	{
		Log::add('Excecute SuperBrightness Custom Filter', Log::DEBUG, 'IFR');

		// Validate that the brightness value exists and is an integer.
		if (!isset($options[IMG_FILTER_BRIGHTNESS]) || !\is_int($options[IMG_FILTER_BRIGHTNESS]))
		{
			throw new \InvalidArgumentException('No valid brightness value was given.  Expected integer.');
		}

		// Perform the brightness filter.
		imagefilter($this->handle, IMG_FILTER_BRIGHTNESS, $options[IMG_FILTER_BRIGHTNESS]);
	}
}
