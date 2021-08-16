<?php
/**
 * @package     Phproberto.Sample
 * @subpackage  Library
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

$composerAutoload = __DIR__ . '/vendor/autoload.php';

if (!file_exists($composerAutoload))
{
	throw new \RuntimeException("Cannot find sample library autoloader");
}

require_once $composerAutoload;

$lang = JFactory::getLanguage();
$lang->load('lib_phproberto_sample', __DIR__);
