<?php

declare(strict_types=1);

namespace WebChemistry\Filter\Components;

use Nette\Application\UI\Control;
use WebChemistry\Filter\DataSource\IDataSource;
use WebChemistry\Filter\FilterOptions;

class Paginator extends Control {

	/** @var int @persistent */
	public $page = 1;

	/** @var \Nette\Utils\Paginator */
	private $paginator;

	/** @var array steps cache */
	private $steps;

	/** @var int|null */
	private $limit;

	/** @var string */
	private $file;

	/** @var array */
	private $snippets;

	/** @var int */
	private $itemCount;

	public function __construct(?int $limit, IDataSource $dataSource, FilterOptions $options) {
		parent::__construct();

		$this->paginator = new \Nette\Utils\Paginator();
		$this->limit = $limit;
		$this->file = $options->paginatorFile ?: __DIR__ . '/templates/paginator.latte';
		$this->snippets = $options->snippets;
		if ($this->limit !== null) {
			$this->itemCount = $dataSource->getItemCount();
		}
	}

	public function render(): void {
		if ($this->limit === null) {
			return;
		}
		$template = $this->getTemplate();
		$template->setFile($this->file);

		$template->paginator = $this->paginator;
		$template->steps = $this->getSteps();
		$template->ajax = (bool) $this->snippets;
		$template->pageCount = $this->paginator->getPageCount();
		$template->page = $this->paginator->getPage();
		$template->use = $this->paginator->getPageCount() > 1;
		$template->prevLink = $this->prevLink();
		$template->nextLink = $this->nextLink();

		$template->render();
	}

	/**
	 * @return int
	 */
	public function getPage(): int {
		$this->getSteps();

		return $this->limit === null ? 1 : $this->paginator->getPage();
	}

	public function getItemsOnPage(): int {
		$this->getSteps();

		return $this->limit === null ? $this->itemCount : $this->paginator->getItemCount();
	}

	/**
	 * @return string|null
	 * @internal
	 */
	public function prevLink(): ?string {
		if ($this->page !== 1) {
			return $this->stepLink($this->page - 1);
		}

		return null;
	}

	/**
	 * @return string|null
	 * @internal
	 */
	public function nextLink(): ?string {
		if ($this->page !== $this->paginator->getPageCount()) {
			return $this->stepLink($this->page + 1);
		}

		return null;
	}

	/**
	 * @param int $step
	 * @return string
	 * @internal
	 */
	public function stepLink($step) {
		if ($this->snippets) {
			return $this->link('paginate!', ['page' => $step]);
		}

		return $this->link('this', ['page' => $step]);
	}

	/**
	 * @internal
	 */
	public function handlePaginate() {
		if ($this->getPresenter()->isAjax()) {
			foreach ($this->snippets as $snippet) {
				$this->getPresenter()->redrawControl($snippet);
			}
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @internal
	 */
	public function resetPage() {
		$this->page = 1;
	}

	public function getOffset(): ?int {
		if ($this->limit === null) {
			return null;
		}
		$this->getSteps();

		return $this->paginator->getOffset();
	}

	/**
	 * @return array
	 */
	public function getSteps(): array {
		if ($this->steps === null) {
			if ($this->limit === null) {
				$this->steps = [];
			} else {
				$this->paginator->setPage((int) $this->page);
				$this->paginator->setItemsPerPage($this->limit);
				$this->paginator->setItemCount($this->itemCount);
				$this->paginator->setPage($this->page);
				$paginator = $this->paginator;
				$arr = range(max($paginator->getFirstPage(), $paginator->getPage() - 2), min($paginator->getLastPage(), $paginator->getPage() + 2));
				$count = 2;
				$quotient = ($paginator->getPageCount() - 1) / $count;
				for ($i = 0; $i <= $count; $i++) {
					$arr[] = (int) (round($quotient * $i) + $paginator->getFirstPage());
				}
				sort($arr);
				$this->steps = array_values(array_unique($arr));
			}
		}

		return $this->steps;
	}

}
