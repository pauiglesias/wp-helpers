<?php

/**
 * Plugin Name:       Package Name
 * Description:       Package description
 *
 * Version:           1.0.0
 * Plugin URI:        https://microdeploy.com
 *
 * Author:            MicroDeploy
 * Author URI:        https://microdeploy.com
 *
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Text Domain:       mdwphl
 *
 * @package Package Name
 * @author  Pau Iglesias <pau@microdeploy.com>
 */

namespace MicroDeploy\Package;

defined('ABSPATH') || die;

const NAME		= 'Package Name';
const SLUG		= 'package-Name';
const FILE 		= __FILE__;
const PREFIX 	= 'mdwphl';
const VERSION 	= '1.0.0';

const DEV		= false;
const DEBUG		= false;

require_once dirname(FILE).'/helpers/autoload.php';

Core\Core::instance();