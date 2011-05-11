<?php
/**
 * Setup the `mm` library
 */
$mm = dirname(dirname(__FILE__)) . DS . 'vendors' . DS . 'mm';

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

/**
 * Image file versions
 */
Configure::write('Filebinder.version.image', array(
	'thumbnail' => array(
		'fit' => array(100 ,100),
		'convert' => 'image/png'
	),
));

/**
 * Video file versions
 */
Configure::write('Filebinder.version.video', array());

/**
 * Audio file versions
 */
Configure::write('Filebinder.version.audio', array());

/**
 * Document file versions
 */
Configure::write('Filebinder.version.document', array());

/**
 * Generic file versions
 */
Configure::write('Filebinder.version.generic', array());