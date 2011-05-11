<?php
class LabelHelper extends AppHelper {

    var $helpers = array('Html', 'Session');

    /**
     * image
     *
     * @param array $file
     * @param array $options
     * @return string
     */
    function image($file = null, $options = array()){
        $url = $this->_makeSrc($file, $options);
        if (!$url) {
            return empty($options['noFile']) ? '' : $options['noFile'];
        }
        unset($options['noFile'], $options['prefix'], $options['conversion'], $options['url']);
        return $this->Html->image($url, $options);
    }

    /**
     * link
     *
     * @param array $file
     * @param array $options
     * @return string
     */
    function link($file = null, $options = array()){
        $url = $this->_makeSrc($file, $options);
        if (!$url) {
            return empty($options['noFile']) ? '' : $options['noFile'];
        }
        $fileTitle = empty($options['title']) ? $file['file_name'] : $options['title'];
        unset($options['title'], $options['noFile'], $options['prefix'], $options['conversion'], $options['url']);
        return $this->Html->link($fileTitle, $url, $options);
    }

    /**
     * url
     *
     * @param array $file
     * @param array $options
     * @return string
     */
    function url($file = null, $options = array()){
        $url = $this->_makeSrc($file, $options);
        if (!$url) {
            return empty($options['noFile']) ? '' : $options['noFile'];
        }
        unset($options['noFile'], $options['prefix'], $options['conversion'], $options['url']);
        return $this->Html->url($url, $options);
    }

    /**
     * _makeSrc
     *
     * Generating the file path.
     * If set `conversion` option, make cache file. (PHP >= 5.2)
     *
     * If using cache, load the plugin's configuration:
     *   //Within your app's bootstrap.php
     *   require APP . 'plugins/filebinder/config/core.php';
     *
     * For example, resize image to `200x200` pixel, and convert to `png`:
     *   $options['version'] = array(
     *       'fit' => array(200, 200),
     *       'convert' => 'image/png'
     *   );
     *
     * Or, if defined instructions of `thumbnail`,
     *
     *   $options['version'] = 'thubmnail';
     *
     * @param array $file
     * @param array $options
     * @return mixed
     * @link http://github.com/davidpersson/mm
     */
    function _makeSrc($file = null, $options = array()){
        $options += array('prefix' => '', 'version' => array(), 'url' => array());
        $filePath = false;

        if (!empty($file['file_path'])) {
            $filePath = preg_replace('#/([^/]+)$#' , '/' . $options['prefix'] . '$1' , $file['file_path']);

        } else if (!empty($file['tmp_bind_path'])) {
            $file['model_id'] = 0;
            $filePath = $file['tmp_bind_path'];
        }

        if (!$filePath) {
            return null;
        }

        if ($options['version'] && !empty($file['cache_dir']) && strpos($file['cache_dir'], WWW_ROOT) === 0) {
            if (!class_exists('VersionFile')) {
                App::import('Lib', 'Filebinder.VersionFile');
            }

            $fileOptions = array(
                'mode' => $file['mode'],
                'dirMode' => $file['dir_mode']
            );

            if (!$filePath = VersionFile::create($filePath, $file['cache_dir'], $options['version'], $fileOptions)) {
                return null;
            }
        }

        if (!preg_match('#' . WWW_ROOT . '#', $filePath)) {
            $fileName = pathinfo($filePath, PATHINFO_BASENAME);
            $url = $options['url'] ? (array)$options['url'] : array();

            if (!$url) {
                $urlPrefixes = Configure::read('Routing.prefixes');

                if (!$urlPrefixes && Configure::read('Routing.admin')) {
                    $urlPrefixes = Configure::read('Routing.admin');
                }

                foreach ((array)$urlPrefixes as $urlPrefix) {
                    $url[$urlPrefix] = false;
                }
            }

            $url += array(
                'plugin' => 'filebinder',
                'controller' => 'filebinder',
                'action' => 'loader',
                Inflector::underscore($file['model']),
                $file['model_id'],
                $file['field_name'],
                Security::hash($file['model'] . $file['model_id'] . $file['field_name'] . $this->Session->read('Filebinder.hash')),
                $fileName
            );

        } else {
            $url = preg_replace('#' . WWW_ROOT . '#', DS, $filePath);
        }

        return $url;
    }
}