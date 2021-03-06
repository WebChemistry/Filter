<?php

declare(strict_types=1);

namespace WebChemistry\Filter\Components;

use Nette\Application\UI\Component;
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

	/** @var IDataSource */
	private $dataSource;

	/** @var bool */
	private $append = false;

	/** @var bool */
	private $ajax = false;

	/** @var callable[] */
	public $onAjax = [];

	public function __construct(?int $limit, IDataSource $dataSource, FilterOptions $options) {
		$this->paginator = new \Nette\Utils\Paginator();
		$this->limit = $limit;
		$this->onAjax = $options->onAjax;
		$this->file = $options->paginatorFile ?: __DIR__ . '/templates/paginator.latte';
		$this->snippets = $options->snippets;
		$this->ajax = $options->ajax;
		$this->dataSource = $dataSource;
		$this->append = $options->append;
	}

	public function getItemCount(): ?int {
		if (!$this->itemCount) {
			if ($this->limit !== null) {
				$this->itemCount = $this->dataSource->getItemCount();
			}
		}

		return $this->itemCount;
	}

	public function render(): void {
		if ($this->limit === null) {
			return;
		}
		$template = $this->getTemplate();
		$template->setFile($this->file);

		$template->paginator = $this->paginator;
		$template->steps = $this->getSteps();
		$template->ajax = $this->ajax;
		$template->pageCount = $this->paginator->getPageCount();
		$template->page = $this->paginator->getPage();
		$template->use = $this->paginator->getPageCount() > 1;
		$template->prevLink = $this->prevLink();
		$template->nextLink = $this->nextLink();
		$template->appendLink = null;

		if ($this->append && $this->limit && $this->page < $this->paginator->getPageCount()) {
			$template->appendLink = $this->link('paginate!', ['page' => $this->page + 1]);
		}

		$template->render();
	}

	public function isFirstPage(): bool {
		return $this->page === 1;
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

		return $this->limit === null ? $this->getItemCount() : $this->paginator->getItemCount();
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
		if ($this->ajax) {
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
				$this->getParent()->getControl()->redrawControl($snippet);
			}
			/*foreach ($this->onAjax as $callback) {
				$callback($this);
			}*/
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
				$this->paginator->setItemCount($this->getItemCount());
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
