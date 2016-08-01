<?php

namespace WebChemistry\Filter;

use Nette;

class Paginator extends Nette\Application\UI\Control {
    
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

	/** @var callable[] */
	public $onRender = [];

	/** @var int */
	private $limit;

	/**
	 * @param Settings $settings
	 */
    public function __construct(Settings $settings) {
        $this->paginator = new Nette\Utils\Paginator;
        $this->settings = $settings;
    }
    
    public function render() {
        $this->template->setFile($this->file);
        $this->template->paginator = $this->paginator;

        $this->template->steps = $this->getSteps();
        $this->template->ajax = $this->settings->isAjax();
        $this->template->pageCount = $this->paginator->pageCount;
		$this->template->page = $this->paginator->page;
		$this->template->use = $this->paginator->pageCount > 1;
		$this->template->prevLink = $this->prevLink();
		$this->template->nextLink = $this->nextLink();

		foreach ($this->onRender as $callback) {
			$callback();
		}

        $this->template->render();
    }

	/**
	 * @return string
	 * @internal
	 */
	public function prevLink() {
		if ($this->page !== 1) {
			return $this->stepLink($this->page - 1);
		}
	}

	/**
	 * @return string
	 * @internal
	 */
	public function nextLink() {
		if ($this->page !== $this->paginator->getPageCount()) {
			return $this->stepLink($this->page + 1);
		}
	}

	/**
	 * @param int $step
	 * @return string
	 * @internal
	 */
	public function stepLink($step) {
		if ($this->settings->getSnippets()) {
			return $this->link('paginate!', ['page' => $step]);
		}

		return $this->link('this', ['page' => $step]);
	}

	/**
	 * @param int $step
	 * @internal
	 */
	public function handlePaginate($step) {
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
    public function resetPage() {
		$this->page = 1;
	}

    /************************* Setters **************************/

	/**
	 * @param string $file
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
            $this->paginator->setItemsPerPage($this->limit);
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
	 * @return bool
	 */
    public function isOk() {
    	return $this->limit !== NULL;
	}

	/**
	 * @param int $limit
	 * @return self
	 */
	public function setLimit($limit) {
		$this->limit = $limit;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * @return int
	 */
    public function getOffset() {
    	if ($this->limit === NULL) {
    		return NULL;
		}
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
