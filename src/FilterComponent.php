<?php

namespace WebChemistry\Filter;

use Nette\Application\IPresenter;
use Nette\Application\UI\Component;
use Nette\Application\UI\PresenterComponent;
use Nette\Forms\Form;

if (!class_exists(Component::class)) {
	class_alias(PresenterComponent::class, Component::class);
}

abstract class FilterComponent extends Component {

	/** @var Settings */
	protected $settings;

	/** @persistent */
	public $filtering = [];

	/** @persistent */
	public $links = [];

	/** @persistent */
	public $limit;

	/** @var bool */
	private $initCompleted = FALSE;

	public function __construct() {
		$this->monitor(IPresenter::class);
		$this->callStartup();
	}

	private function callStartup() {
		if ($this->settings === NULL) {
			$this->settings = new Settings($this);
			$this->startup();
		}
	}

	abstract protected function startup();

	protected function attached($presenter) {
		parent::attached($presenter);

		if ($presenter instanceof IPresenter) {
			$this->callStartup();
			if ($presenter->isAjax()) {
				foreach ($this->settings->getSnippets() as $snippet) {
					$presenter->redrawControl($snippet);
				}
			}
		}
	}

	/**
	 * @return DataFacade
	 */
	public function createFacade() {
		return new DataFacade($this, $this->settings);
	}

	/**
	 * @param Form $form
	 * @param array$values
	 * @internal
	 */
	public function successForm(Form $form, array $values) {
		$this->filtering = array_merge($this->filtering, $values);
		$this->getPaginator()->resetPage();

		if ($this->getPresenter()->isAjax()) {
			foreach ($this->settings->getSnippets() as $snippet) {
				$this->getPresenter()->redrawControl($snippet);
			}
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @internal
	 */
	public function init() {
		if (!$this->initCompleted) {
			$this->initCompleted = TRUE;
			$this->callStartup();
			if ($this->settings->isDynamicLimit() && $this->settings->getPaginator()->isOk() && $this->limit > 0) {
				$this->settings->getPaginator()->setLimit($this->limit);
			}
			$this->settings->getDataSource()->__callDataSource($this->getFilterValues());
		}
	}

	/**
	 * @return array
	 */
	public function getData() {
		$this->init();

		$additional = $this->settings->getAdditional();
		if ($additional->isAdditional()) {
			foreach ($this->settings->getDataSource()->getDataSource()->getData($this->getPaginator()->getLimit(), $this->getPaginator()->getOffset()) as $item) {
				$additional->setActiveRow($item);
				yield $item;
			}
		} else {
			foreach ($this->settings->getDataSource()->getDataSource()->getData($this->getPaginator()->getLimit(), $this->getPaginator()->getOffset()) as $item) {
				yield $item;
			}
		}
		$additional->setActiveRow(NULL);
	}

	/**
	 * @return bool
	 */
	public function isFiltered() {
		return (bool) $this->filtering;
	}

	/**
	 * @return array
	 */
	public function getFilterValues() {
		return array_merge($this->settings->getDefaultFilterData(), (array) $this->filtering, $this->settings->getLinks()->parse($this->links));
	}

	/************************* Links **************************/

	public function limitLink($difference) {
		$limit = $this->limit ?: $this->getPaginator()->getLimit();

		return $this->link('this', ['limit' => $limit + $difference]);
	}

	/**
	 * @internal
	 */
	public function getDynamicLink($link, $value) {
		if (!$this->settings->getLinks()->exists($link)) {
			throw new FilterException("Link $link not exists.");
		}

		return $this->link('this', ['links' => array_merge($this->links, [$link => $value])]);
	}

	/************************* Reset **************************/

	public function handleReset() {
		$this->filtering = [];
		$this->limit = NULL;
		$this->links = [];
		$this->getPaginator()->resetPage();

		if (($snippets = $this->settings->getSnippets()) && $this->getPresenter()->isAjax()) {
			foreach ($snippets as $snippet) {
				$this->getPresenter()->redrawControl($snippet);
			}
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @return string
	 */
	public function getResetLink() {
		return $this->link('reset!');
	}

	/************************* Components **************************/

	/**
	 * @return FormList
	 * @internal
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
	 * @internal
	 */
	protected function createComponentPaginator() {
		$paginator = $this->settings->getPaginator();
		$paginator->onRender[] = [$this, 'init'];

		return $paginator;
	}

}
