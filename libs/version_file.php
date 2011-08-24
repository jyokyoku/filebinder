<?php
class VersionFile extends Object
{
	public static function create($filePath, $dir, $instructions = array(), array $options = array())
	{
		$options += array('mode' => 0644, 'dirMode' => 0755);

		if (
			!is_file($filePath)
			|| !is_array($instructions)
			|| empty($instructions)
			|| empty($dir)
			|| (!is_dir($dir) && !mkdir($dir, $options['dirMode'], true))
		) {
			return false;
		}

		if (isset($instructions['convert'])) {
			$extension = Mime_Type::guessExtension($instructions['convert']);

		} else {
			$extension = Mime_Type::guessExtension($filePath);
		}

		$versionFilePath = $dir . DS . Security::hash(serialize($instructions), 'md5') . '.' . $extension;
		$cached = false;

		if (is_file($versionFilePath)) {
			if (filemtime($filePath) <= filemtime($versionFilePath)) {
				$cached = true;
			}
		}

		if (!$cached) {
			$Media = Media_Process::factory(array('source' => $filePath));

			foreach (Set::normalize($instructions) as $method => $args) {
				if (method_exists($Media, $method)) {
					$result = call_user_func_array(array($Media, $method), (array)$args);

				} else {
					$result = $Media->passthru($method, $args);
				}

				if ($result === false) {
					return false;

				} else if (is_a($result, 'Media_Process_Generic')) {
					$Media = $result;
				}
			}

			if (!$Media->store($versionFilePath, array('overwrite' => true)) || !chmod($versionFilePath, $options['mode'])) {
				return false;
			}
		}

		return $versionFilePath;
	}
}