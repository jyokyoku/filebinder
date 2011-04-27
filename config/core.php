<?php
$mm = dirname(dirname(__FILE__)) . DS . 'libs' . DS . 'mm';

if (strpos(ini_get('include_path'), $mm) === false) {
	ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . $mm . DS . 'src');
}

require_once 'Mime/Type.php';
require_once 'Media/Process.php';
require_once 'Media/Info.php';

Mime_Type::config('Magic', array(
	'adapter' => 'Freedesktop',
	'file' => $mm . DS . 'data' . DS . 'magic.db'
));

Mime_Type::config('Glob', array(
	'adapter' => 'Freedesktop',
	'file' => $mm . DS . 'data' . DS . 'glob.db'
));

Media_Process::config(array(
	'image' => 'Gd',
));

Media_Info::config(array(
	'image' => array('ImageBasic'),
));