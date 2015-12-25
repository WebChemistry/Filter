<?php

namespace WebChemistry\Filter;

use Nette;

/**
 * @property-read array $steps
 * @property-read int $offset
 * @property-write array $snippet
 * @property-write string $file
 * @property-write int $itemsPerPage
 * @property-write int $count
 * @method onRender
 */
class Paginator extends Nette\Application\UI\Control implements IComponent{
    
    /** @var Nette\Utils\Paginator */
    private $paginator;
    
    /** @var string */
    private $file;
    
    /** @persistent */
    public $page = 1;

    /** @var array */
    private $steps = array();

    /** @var Settings */
    private $settings;

	/** @var array */
	public $onRender = [];

    public function __construct(Settings $settings) {
        $this->paginator = new Nette\Utils\Paginator;
        $this->settings = $settings;
    }
    
    public function render() {
        $this->template->setFile($this->settings->getPaginatorFile());
        $this->template->paginator = $this->paginator;

        $this->template->steps = $this->getSteps();
        $this->template->ajax = (bool) $this->settings->getSnippet();
        $this->template->pageCount = $this->paginator->pageCount;
		$this->template->page = $this->paginator->page;
		$this->template->use = $this->paginator->pageCount > 1;
		$this->template->prevLink = $this->prevLink();
		$this->template->nextLink = $this->nextLink();

		$this->onRender();

        $this->template->render();
    }

	public function prevLink() {
		$isAjax = (bool) $this->settings->getSnippet();

		if ($this->page !== 1) {
			if ($isAjax) {
				return $this->link('paginate!', ['page' => $this->page - 1]);
			} else {
				return $this->link('this', ['page' => $this->page - 1]);
			}
		}
	}

	public function nextLink() {
		$isAjax = (bool) $this->settings->getSnippet();

		if ($this->page !== $this->paginator->pageCount) {
			if ($isAjax) {
				return $this->link('paginate!', ['page' => $this->page + 1]);
			} else {
				return $this->link('this', ['page' => $this->page + 1]);
			}
		}
	}

	public function stepLink($step) {
		$isAjax = (bool) $this->settings->getSnippet();

		if ($isAjax) {
			return $this->link('paginate!', ['page' => $step]);
		} else {
			return $this->link('this', ['page' => $step]);
		}
	}

	public function handlePaginate($step) {
		if ($this->presenter->isAjax()) {
			foreach ($this->settings->getSnippet() as $snippet) {
				$this->presenter->redrawControl($snippet);
			}
		} else {
			$this->redirect('this');
		}
	}

    public function init() {
        $this->settings->setPage($this->page);
        $this->settings->setOffset($this->getOffset());
    }

    public function resetPage() {
		$this->page = 1;
	}

    /************************* Setters **************************/

    public function setFile($file) {
        $this->file = $file;
    }

    /************************* Getters **************************/
    
    /**
     * @return array
     */
    public function getSteps() {
        if (!$this->steps) {
            $this->paginator->setPage($this->page);
            $this->paginator->setItemsPerPage($this->settings->getLimit());
            $this->paginator->setItemCount($this->settings->getItemCount());
            $this->paginator->setPage($this->page);

            $paginator = $this->paginator;

            $arr = range(
                    max($paginator->getFirstPage(), $paginator->getPage() - 2), 
                    min($paginator->getLastPage(), $paginator->getPage() + 2)
            );

            $count = 2;
            $quotient = ($paginator->getPageCount() - 1) / $count;

            for ($i = 0; $i <= $count; $i++) {
                $arr[] = (int) (round($quotient * $i) + $paginator->getFirstPage());
            }

            sort($arr);

            $this->steps = array_values(array_unique($arr));
        }
        
        return $this->steps;
    }

    public function getOffset() {
        $this->getSteps();

        return $this->paginator->getOffset();
    }

    /**
     * @return Nette\Utils\Paginator
     */
    public function getPaginator() {
        return $this->paginator;
    }
}
