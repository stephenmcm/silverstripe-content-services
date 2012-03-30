<?php

/**
 * A content writer that writes data to disk
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class FileContentWriter extends ContentWriter {
	
	/** 
	 * Where should file assets be written to initially? 
	 * 
	 * @var string
	 */
	public static $base_path = 'content';
	
	public function nameToId($fullname) {
		$name = basename($fullname);
		$idPath = md5($fullname);
		$first = substr($idPath, 0, 3);
		$second = substr($idPath, 3, 29);
		return "$first/$second/$name";
	}

	public function write($content = null, $name = '') {
		$docopy = false;
		$reader = $this->getReaderWrapper($content);
		$target = $this->getTarget($name);
		// SS specific
		Filesystem::makeFolder(dirname($target));
		if ($docopy) {
			@copy($content, $target);
		} else {
			file_put_contents($target, $reader->read());
		}
	}

	protected function getTarget($fullname) {
		// if we've got an ID, it means we're doing an overwrite, and in that case
		// the path is encoded in the ID
		if (!$this->id) {
			// set our ID
			$this->id = $this->nameToId($fullname);
		}

		if (!strlen($fullname)) {
			throw new Exception("Cannot write unnamed file data");
		}

		// SS specific bit here
		if (self::$base_path{0} == '/') {
			$path = self::$base_path . '/' . $this->id; 
		} else {
			$path = Director::baseFolder() . '/' . self::$base_path . '/' . $this->id; 
		}

		return $path;
	}
}
