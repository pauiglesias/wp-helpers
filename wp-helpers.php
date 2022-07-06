<?php
/*
Plugin Name: GTR - Reports Content Client
Plugin URI: http://www.grupotecmared.es
Description: Reports Content Client
Version: 1.0.0
Author: Pau Iglesias
License: GPL3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

namespace MicroDeploy\Package;

defined('ABSPATH') || die;

const FILE = __FILE__;
const PREFIX = 'gtrrcc';
const VERSION = '1.0.0';

require_once dirname(FILE).'/helpers/autoload.php';

Core\Core::instance();