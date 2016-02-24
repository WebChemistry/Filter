<?php

namespace WebChemistry\Filter;

use Nette\Application\IPresenter;
use Nette\Application\UI\PresenterComponent;
use Nette\Forms\Form;

abstract class FilterComponent extends PresenterComponent {

	/** @var Settings */
	protected $settings;

	/** @persistent */
	public $filtering = [];

	/** @var bool */
	protected $initCompleted = FALSE;

	/** @var bool */
	private $startupCompleted = FALSE;

	public function __construct() {
		$this->monitor('Nette\Application\IPresenter');
		$this->callStartup();
	}

	private function callStartup() {
		if ($this->startupCompleted || $this->settings !== NULL) {
			return NULL;
		}

		$this->settings = new Settings();
		$this->startup($this->settings);
		if (!$this->settings instanceof Settings) {
			throw new Exception('Settings must be instance of WebChemistry\Filter\Settings.');
		}

		$this->startupCompleted = TRUE;
	}

	/**
	 * @return Settings|void
	 */
	abstract protected function startup(Settings $settings);

	protected function attached($presenter) {
		parent::attached($presenter);

		if ($presenter instanceof IPresenter) {
			$this->callStartup();
		}
	}

	/**
	 * @param Form $form
	 * @param array$values
	 * @internal
	 */
	public function successForm(Form $form, array $values) {
		$this->filtering = $values + $this->filtering;
		$this->getPaginator()->resetPage();

		if ($this->getPresenter()->isAjax() && $this->settings->isAjaxForm() && $snippets = $this->settings->getSnippet()) {
			foreach ($snippets as $snippet) {
				$this->getPresenter()->redrawControl($snippet);
			}
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @return Settings
	 */
	public function getSettings() {
		return $this->settings;
	}

	/**
	 * @return \WebChemistry\Filter\ComponentForm
	 */
	protected function createComponentForms() {
		return $this->settings->getForms();
	}

	/**
	 * @return Paginator
	 */
	public function getPaginator() {
		return $this['paginator'];
	}

	/**
	 * @return Paginator
	 */
	protected function createComponentPaginator() {
		$paginator = new Paginator($this->settings);

		$paginator->onRender[] = [$this, 'init'];

		return $paginator;
	}

	/**
	 * @internal
	 */
	public function init() {
		if ($this->initCompleted) {
			return NULL;
		}
		$this->initCompleted = TRUE;

		$this->settings->callDataSource($this->filtering);
		$this->getPaginator()->init();
	}

	/**
	 * @return mixed
	 * @throws \WebChemistry\Filter\Exception
	 */
	public function getData() {
		$this->init();

		return $this->settings->getData();
	}

	/**
	 * @return bool
	 */
	public function isFiltering() {
		return (bool) $this->filtering;
	}

	/**
	 * @return int
	 */
	public function getItemCount() {
		$this->init();

		return $this->settings->getItemCount() < $this->settings->getLimit() ? $this->settings->getLimit() : $this->settings->getItemCount();
	}

	/**
	 * @return int
	 */
	public function getLimit() {
		return $this->settings->getLimit();
	}

	/**
	 * @return int
	 */
	public function getPage() {
		return $this['paginator']->page;
	}

	/**
	 * @return string
	 */
	public function getResetLink() {
		return $this->link('reset!');
	}

	public function handleReset() {
		$this->filtering = [];
		$this->getPaginator()->resetPage();

		if (($snippets = $this->settings->getSnippet()) && $this->getPresenter()->isAjax()) {
			foreach ($snippets as $snippet) {
				$this->getPresenter()->redrawControl($snippet);
			}
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @return bool
	 */
	public function useCache() {
		return !$this->isFiltering() || $this->settings->isCacheFiltering();
	}

	/**
	 * @return string
	 */
	public function getCacheId() {
		if ($this->isFiltering()) {
			if (!$this->settings->isCacheFiltering()) {
				return FALSE;
			}
			sort($this->filtering);
			$filterHash = '.filter.' . md5(serialize($this->filtering));
		} else {
			$filterHash = NULL;
		}

		return $this->getUniqueId() . '.page.' . $this->getPage() . $filterHash;
	}

	/**
	 * Use only for macro from WebChemistry\Filter\Cache
	 *
	 * @return array
	 */
	public function getCache() {
		if ($this->isFiltering() && !$this->settings->isCacheFiltering()) {
			return [NULL, 'if' => FALSE];
		}
		$args = $this->settings->getCacheArgs();
		$args[0] = $this->getCacheId();

		return $args;
	}

}
