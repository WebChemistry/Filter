<?php

declare(strict_types=1);

namespace WebChemistry\Filter;

use Doctrine\ORM\QueryBuilder;
use Kdyby\Translation\ITranslator;
use Nette\Application\IPresenter;
use Nette\Application\UI\ComponentReflection;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use WebChemistry\Filter\Components\ComponentWrapper;
use WebChemistry\Filter\Components\FormBuilder;
use WebChemistry\Filter\Components\Paginator;
use WebChemistry\Filter\DataSource\DoctrineDataSource;
use WebChemistry\Filter\DataSource\IDataSource;

class Filter extends Control {

	/** @var IDataSource */
	private $dataSource;

	/** @var Paginator */
	private $paginator;

	/** @var FilterFacade */
	private $filterFacade;

	/** @var FilterValues */
	private $filterValues;

	/** @var ITranslator */
	private $translator;

	/** @var iterable */
	private $data = false;

	/** @var FilterOptions */
	private $filterOptions;

	/** @var FilterParameters */
	private $filterParams;

	/** @var FormBuilder[] */
	private $forms = [];

	/** @var callable[] */
	public $onFetch = [];

	public function __construct(FilterOptions $filterOptions, ?ITranslator $translator = null) {
		parent::__construct();

		$this->translator = $translator;
		$this->filterOptions = $filterOptions;
		$this->filterParams = new FilterParameters($filterOptions);
		$this->filterValues = new FilterValues($this->filterOptions, $this->filterParams);

		$this->init();
	}

	private function init(): void {
		foreach ($this->filterOptions->forms as $name => $callback) {
			$this->forms[$name] = $builder = new FormBuilder($this->filterOptions, $this->filterParams);
			$callback($builder);
		}
	}

	private function whenAttached(): void {
		// forms
		$this->addComponent($wrapper = new ComponentWrapper(), 'forms');
		$defaults = $this->filterValues->getFilterValues();
		foreach ($this->forms as $name => $builder) {
			$form = $builder->getForm();

			$form->addSubmit('send');

			if ($this->translator) {
				$form->setTranslator($this->translator);
			}
			$form->setDefaults($defaults, true);
			$form->onSuccess[] = function (Form $_, array $values): void {
				$this->filterParams->setFilters($values);

				$this->redirect('this');
			};

			$wrapper->addComponent($form, $name);
		}

		// datasource
		$this->createDataSource();

		// paginator
		$this->attachPaginator();
	}

	public function loadState(array $params): void {
		parent::loadState($params);

		$this->filterParams->loadState($params);
	}

	public function saveState(array &$params, ComponentReflection $reflection = null): void {
		parent::saveState($params, $reflection);

		$this->filterParams->saveState($params);
	}

	private function createDataSource(): void {
		$callback = $this->filterOptions->source;
		$source = $callback($this->filterValues);
		if ($source instanceof QueryBuilder) {
			$this->dataSource = new DoctrineDataSource($source, $this->filterOptions->sourceOptions);
		} else {
			throw new FilterException('Unsupported data source.');
		}
	}

	/**
	 * @return IDataSource
	 */
	public function getDataSource(): IDataSource {
		return $this->dataSource;
	}

	private function attachPaginator(): void {
		$this->paginator = new Paginator(
			$this->filterValues->getLimitPerPage(), $this->dataSource, $this->filterOptions
		);

		$this->addComponent($this->paginator, 'paginator');
	}

	protected function attached(IComponent $obj): void {
		parent::attached($obj);

		if ($obj instanceof IPresenter) {
			$this->whenAttached();
		}
	}

	public function getData(): iterable {
		if ($this->data === false) {
			$this->data = $this->dataSource->getData($this->filterValues->getLimitPerPage(), $this->paginator->getOffset());
			foreach ($this->onFetch as $fetch) {
				$fetch($this->data);
			}
		}

		return $this->data;
	}

	public function getFacade(): FilterFacade {
		if (!$this->filterFacade) {
			$this->filterFacade = new FilterFacade($this, $this->dataSource, $this->filterValues);
		}

		return $this->filterFacade;
	}

	public function getResetLink(): string {
		$this->filterParams->resetState();

		return $this->link('this');
	}

	public function handleLink(string $name, $val): void {
		$this->filterParams->addLink($name, $val);

		$this->redirect('this');
	}

	// components

	protected function createComponentLimitPerPage() {
		$form = new Form();

		$input = $form->addSelect('limitPerPage', null, $this->filterOptions->limits);
		$input->checkAllowedValues = false;
		$input->setDefaultValue($this->filterValues->getLimitPerPage());

		$form->addSubmit('send');

		$form->onSuccess[] = function (Form $form, array $values): void {
			$this->filterParams->setLimitPerPage($values['limitPerPage']);

			$this->redirect('this');
		};

		return $form;
	}

	protected function createComponentOrder(): Form {
		$form = new Form();
		$form->setTranslator($this->translator);

		$form->addSelect('order', null, $this->filterOptions->orderValues)
			->setDefaultValue($this->filterValues->getOrderKey());

		$form->addSubmit('send');

		$form->onSuccess[] = function (Form $form, array $values): void {
			$this->filterParams->setOrder($values['order']);

			$this->redirect('this');
		};

		return $form;
	}

	protected function createComponentSearch(): Form {
		$form = new Form();
		$form->setTranslator($this->translator);

		$form->addText('search')
			->setDefaultValue($this->filterValues->getSearch())
			->setNullable();

		$form->addSubmit('send');

		$form->onSuccess[] = function (Form $form, array $values): void {
			$this->filterParams->setSearch($values['search']);

			$this->redirect('this');
		};

		return $form;
	}

}
