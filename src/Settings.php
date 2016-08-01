<?php

namespace WebChemistry\Filter;

use Nette\Utils\ObjectMixin;
use WebChemistry\Filter\DataSource;

/**
 * @property-read Paginator $paginator
 * @property-read Links $links
 * @property-read FormList $forms
 * @property-read Additional $additional
 */
class Settings {

	/** @var string */
	public static $defaultPaginationTemplate = __DIR__ . '/templates/paginator.latte';

	/** @var array */
	private $snippet = [];

	/** @var int */
	private $itemCount;

	/** @var DataSource\DataSourceFacade */
	private $dataSource;

	/** @var array */
	private $defaultFilterData = [];

	/** @var FormList */
	private $forms;

	/** @var bool */
	private $dynamicLimit = FALSE;

	/** @var Links */
	private $links;

	/** @var Paginator */
	private $paginator;

	/** @var Additional */
	private $additional;

	public function __construct() {
		$this->forms = new FormList();
		$this->links = new Links();
		$this->paginator = new Paginator($this);
		$this->paginator->setFile(self::$defaultPaginationTemplate);
		$this->dataSource = new DataSource\DataSourceFacade();
		$this->additional = new Additional();
	}

	/**
	 * @param bool $dynamicLimit
	 * @return self
	 */
	public function setDynamicLimit($dynamicLimit = TRUE) {
		$this->dynamicLimit = $dynamicLimit;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isDynamicLimit() {
		return $this->dynamicLimit;
	}

	/**
	 * @return bool
	 */
	public function isAjax() {
		return (bool) $this->snippet;
	}

	/**
	 * @return array
	 */
	public function getSnippets() {
		return (array) $this->snippet;
	}

	/**
	 * @param string|array $snippet
	 * @return Settings
	 */
	public function setSnippet($snippet) {
		$this->snippet = (array) $snippet;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getItemCount() {
		$this->dataSource->check();
		if (!$this->itemCount) {
			$this->itemCount = $this->dataSource->getDataSource()->getCount();
		}

		return $this->itemCount;
	}

	/**
	 * @return DataSource\DataSourceFacade
	 */
	public function getDataSource() {
		return $this->dataSource;
	}

	/**
	 * @param array $defaultFilterData
	 * @return self
	 */
	public function setDefaultFilterData(array $defaultFilterData) {
		$this->defaultFilterData = $defaultFilterData;

		return $this;
	}

	/**
	 * @return array
	 * @internal
	 */
	public function getDefaultFilterData() {
		return $this->defaultFilterData;
	}

	/**
	 * @return FormList
	 */
	public function getForms() {
		return $this->forms;
	}

	/**
	 * @return Links
	 */
	public function getLinks() {
		return $this->links;
	}

	/**
	 * @return Paginator
	 */
	public function getPaginator() {
		return $this->paginator;
	}

	/**
	 * @return Additional
	 */
	public function getAdditional() {
		return $this->additional;
	}

	/************************* Magic **************************/

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) {
		return ObjectMixin::get($this, $name);
	}

}
