<?php

namespace WebChemistry\Filter;

class Additional extends \stdClass {

	/** @var callable[] */
	private $callbacks = [];

	/** @var mixed */
	private $activeRow;

	/**
	 * @param callable $callback
	 */
	public function add(callable $callback, $name) {
		$this->callbacks[$name] = $callback;
	}

	/**
	 * @return bool
	 */
	public function isAdditional() {
		return (bool) $this->callbacks;
	}

	/**
	 * @param mixed $activeRow
	 */
	public function setActiveRow($activeRow) {
		$this->activeRow = $activeRow;
	}

	/**
	 * @param string $name
	 * @throws FilterException
	 * @return mixed
	 */
	public function call($name) {
		if (!isset($this->callbacks[$name])) {
			throw new FilterException("Additional data $name not exists.");
		}
		if (!$this->activeRow) {
			throw new FilterException('Active row is not set.');
		}

		$callback = $this->callbacks[$name];

		return $callback($this->activeRow);
	}

	public function __get($name) {
		return $this->call($name);
	}

}
