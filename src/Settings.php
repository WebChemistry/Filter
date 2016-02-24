<?php

namespace WebChemistry\Filter;

use Kdyby\Doctrine\QueryBuilder;
use Nette\Database\Table\Selection;
use Nette\Forms\Form;
use Nette\Object;
use Nette\Utils\Callback;
use WebChemistry\Filter\DataSource\Doctrine;
use WebChemistry\Filter\DataSource\IDataSource;
use WebChemistry\Filter\DataSource\Nette;

/**
 * @property int $limit
 * @property array $snippet
 * @property int $page
 * @property array $filteringDefaults
 * @property string $paginatorFile
 * @property bool $cacheFiltering
 * @property array $cacheArgs
 * @property bool $ajaxForm
 */
class Settings extends Object {

	/** @var array */
	private $snippet = [];

	/** @var int */
	private $limit;

	/** @var int */
	private $page;

	/** @var int */
	private $itemCount;

	/** @var callable */
	private $dataSource;

	/** @var int */
	private $offset;

	/** @var array */
	private $filteringDefaults = [];

	/** @var string */
	public static $defaultPaginatorFile;

	/** @var string */
	private $paginatorFile;

	/** @var bool */
	private $cacheFiltering = FALSE;

	/** @var ComponentForm */
	private $forms;

	/** @var array */
	private $cacheArgs = [];

	/** @var bool */
	private $ajaxForm = FALSE;

	public function __construct() {
		self::$defaultPaginatorFile = self::$defaultPaginatorFile ? : __DIR__ . '/Paginator/templates/paginator.latte';
		$this->forms = new ComponentForm($this);
	}

	/**
	 * @return array
	 */
	public function getSnippet() {
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
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * @param int|null $limit
	 * @return \WebChemistry\Filter\Settings
	 * @throws \WebChemistry\Filter\Exception
	 */
	public function setLimit($limit) {
		if (!is_numeric($limit) && $limit !== NULL) {
			throw new Exception('Limit must be an integer or null.');
		}

		$this->limit = $limit;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * @param int $page
	 * @return \WebChemistry\Filter\Settings
	 * @throws \WebChemistry\Filter\Exception
	 */
	public function setPage($page) {
		if (!is_int($page)) {
			throw new Exception('Page must be an integer.');
		}

		$this->page = $page;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getItemCount() {
		if (!$this->itemCount) {
			$this->itemCount = $this->dataSource->getCount();
		}

		return $this->itemCount;
	}

	/**
	 * @return callable
	 */
	public function getDataSource() {
		return $this->dataSource;
	}

	/**
	 * @internal
	 */
	public function callDataSource(array $filtering) {
		if ($this->dataSource instanceof IDataSource) {
			return NULL;
		}

		$dataSource = call_user_func($this->dataSource, $filtering + $this->filteringDefaults);
		if ($dataSource instanceof Selection) {
			$this->dataSource = new Nette($dataSource, $this);
		} else if ($dataSource instanceof QueryBuilder) {
			$this->dataSource = new Doctrine($dataSource, $this);
		} else {
			throw new Exception('Bad datasource.');
		}

		return $this->dataSource;
	}

	/**
	 * @param callable $dataSource
	 * @return \WebChemistry\Filter\Settings
	 * @throws \WebChemistry\Filter\Exception
	 */
	public function setDataSource($dataSource) {
		if ($this->dataSource instanceof IDataSource) {
			throw new Exception('DataSource already set.');
		}

		$this->dataSource = Callback::check($dataSource);

		return $this;
	}

	/**
	 * @return mixed
	 * @throws \WebChemistry\Filter\Exception
	 */
	public function getData() {
		if (!$this->dataSource instanceof IDataSource) {
			throw new Exception('DataSource was not called.');
		}

		return $this->dataSource->getData();
	}

	/**
	 * @return \WebChemistry\Filter\Filter
	 */
	public function createFilter() {
		return new Filter($this);
	}

	/**
	 * @return int
	 */
	public function getOffset() {
		return $this->offset;
	}

	/**
	 * @param int $offset
	 * @return \WebChemistry\Filter\Settings
	 * @throws \WebChemistry\Filter\Exception
	 */
	public function setOffset($offset) {
		if (!is_int($offset)) {
			throw new Exception('Offset must be an integer.');
		}

		$this->offset = $offset;

		return $this;
	}

	/**
	 * @param array $filteringDefaults
	 * @return Settings
	 */
	public function setFilteringDefaults(array $filteringDefaults) {
		$this->filteringDefaults = $filteringDefaults;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPaginatorFile() {
		return $this->paginatorFile ? $this->paginatorFile : self::$defaultPaginatorFile;
	}

	/**
	 * @param string $paginatorFile
	 * @return \WebChemistry\Filter\Settings
	 * @throws \WebChemistry\Filter\Exception
	 */
	public function setPaginatorFile($paginatorFile) {
		if (!file_exists($paginatorFile) || is_dir($paginatorFile)) {
			throw new Exception("File '$paginatorFile' is not exists.");
		}

		$this->paginatorFile = $paginatorFile;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isCacheFiltering() {
		return $this->cacheFiltering;
	}

	/**
	 * @param boolean $cacheFiltering
	 * @return Settings
	 */
	public function setCacheFiltering($cacheFiltering) {
		$this->cacheFiltering = (bool) $cacheFiltering;

		return $this;
	}

	/**
	 * @return ComponentForm
	 */
	public function getForms() {
		return $this->forms;
	}

	/**
	 * @param callable|Form $form
	 * @param string $name
	 * @return Settings
	 * @throws Exception
	 */
	public function addForm($form, $name) {
		if (is_callable($form)) {
			$form = $form();
		}

		if (!$form instanceof Form) {
			throw new Exception(printf('Form must be instance of Nette\Forms\Form, given %s', Exception::getType($form)));
		}

		$this->forms->addComponent($form, $name);

		return $this;
	}

	/**
	 * @return array
	 */
	public function getCacheArgs() {
		return $this->cacheArgs;
	}

	/**
	 * @param array $cacheArgs
	 * @return Settings
	 */
	public function setCacheArgs(array $cacheArgs) {
		$this->cacheArgs = $cacheArgs;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isAjaxForm() {
		return $this->ajaxForm;
	}

	/**
	 * @param boolean $ajaxForm
	 * @return Settings
	 */
	public function setAjaxForm($ajaxForm) {
		$this->ajaxForm = (bool) $ajaxForm;

		return $this;
	}

}
