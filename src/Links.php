<?php

namespace WebChemistry\Filter;

class Links {

	/** @var array */
	private $links = [];

	/**
	 * @param string $name
	 */
	public function add($name) {
		$this->links[$name] = TRUE;
	}

	/**
	 * @param string $name
	 */
	public function remove($name) {
		unset($this->links[$name]);
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function exists($name) {
		return array_key_exists($name, $this->links);
	}

	/**
	 * @internal
	 * @return array
	 */
	public function parse(array $values) {
		$return = [];
		foreach ($this->links as $name => $default) {
			if (array_key_exists($name, $values)) {
				$return[$name] = $values[$name];
			}
		}

		return $return;
	}

}
