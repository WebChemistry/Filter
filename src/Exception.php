<?php

namespace WebChemistry\Filter;

class Exception extends \Exception {

	/**
	 * @param $variable
	 * @return string
	 */
	public static function getType($variable) {
		$type = gettype($variable);

		if ($type === 'object') {
			return get_class($variable);
		} else {
			return $type;
		}
	}
}