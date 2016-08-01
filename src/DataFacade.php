<?php

namespace WebChemistry\Filter;

use Nette\Forms\Form;
use Nette\Utils\ObjectMixin;

class DataFacade implements \ArrayAccess {

	/** @var FilterComponent */
	private $filterComponent;

	/** @var Settings */
	private $settings;

	public function __construct(FilterComponent $filterComponent, Settings $settings) {
		$this->filterComponent = $filterComponent;
		$filterComponent->init();
		$this->settings = $settings;
	}

	/**
	 * @param int $difference
	 * @return string
	 */
	public function limitLink($difference) {
		return $this->filterComponent->limitLink($difference);
	}

	/**
	 * @return string
	 */
	public function resetLink() {
		return $this->filterComponent->getResetLink();
	}

	/**
	 * @param string $name
	 * @param mixed $val
	 * @return string
	 */
	public function dynamicLink($name, $val) {
		return $this->filterComponent->getDynamicLink($name, $val);
	}

	/************************* Getters **************************/

	public function getShowingFrom() {
		$from = $this->getLimit() * ($this->getPage() - 1) + 1;

		return $from < $this->getItemCount() ? $from : $this->getItemCount();
	}

	public function getShowingTo() {
		$to = $this->getLimit() * $this->getPage();

		return $to < $this->getItemCount() ? $to : $this->getItemCount();
	}

	/**
	 * @return int
	 */
	public function getCurrentCount() {
		return $this->getLimit() < $this->getItemCount() ? $this->getLimit() : $this->getItemCount();
	}

	/**
	 * @return int
	 */
	public function getLimit() {
		return $this->filterComponent->getPaginator()->getLimit();
	}

	/**
	 * @return int
	 */
	public function getItemCount() {
		return $this->settings->getItemCount();
	}

	/**
	 * @return int
	 */
	public function getPage() {
		return $this->filterComponent->getPaginator()->page;
	}

	/**
	 * @return array
	 */
	public function getData() {
		return $this->filterComponent->getData();
	}

	/**
	 * @return Paginator
	 */
	public function getPaginator() {
		return $this->filterComponent->getPaginator();
	}

	/**
	 * @return Additional
	 */
	public function getAdditional() {
		return $this->settings->getAdditional();
	}

	/************************* Magic **************************/

	public function __get($name) {
		return ObjectMixin::get($this, $name);
	}

	/************************* ArrayAccess **************************/

	/**
	 * @param string $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		return (bool) $this->filterComponent['forms']->getComponent($offset, FALSE);
	}

	/**
	 * @param string $offset
	 * @return Form
	 */
	public function offsetGet($offset) {
		return $this->filterComponent['forms']->getComponent($offset);
	}

	public function offsetSet($offset, $value) {
		throw new FilterException('Method offsetSet is not allowed.');
	}

	public function offsetUnset($offset) {
		throw new FilterException('Method offsetUnset is not allowed.');
	}

}
