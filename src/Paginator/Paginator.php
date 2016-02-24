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
    private $steps = [];

    /** @var Settings */
    private $settings;

	/** @var array */
	public $onRender = [];

	/**
	 * @param Settings $settings
	 */
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

	/**
	 * @return string
	 */
	public function prevLink() {
		if ($this->page !== 1) {
			return $this->stepLink($this->page - 1);
		}
	}

	/**
	 * @return string
	 */
	public function nextLink() {
		if ($this->page !== $this->paginator->getPageCount()) {
			return$this->stepLink($this->page + 1);
		}
	}

	/**
	 * @param int $step
	 * @return string
	 */
	public function stepLink($step) {
		if ($this->settings->getSnippet()) {
			return $this->link('paginate!', ['page' => $step]);
		}

		return $this->link('this', ['page' => $step]);
	}

	/**
	 * @param int $step
	 */
	public function handlePaginate($step) {
		if ($this->getPresenter()->isAjax()) {
			foreach ($this->settings->getSnippet() as $snippet) {
				$this->getPresenter()->redrawControl($snippet);
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

	/**
	 * @param $file
	 * @return self
	 */
    public function setFile($file) {
        $this->file = $file;

		return $this;
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

	/**
	 * @return int
	 */
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
