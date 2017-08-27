<?php

declare(strict_types=1);

namespace WebChemistry\Filter;

use Nette\Application\UI\Form;
use Nette\ComponentModel\Container;
use Nette\Utils\ObjectHelpers;
use WebChemistry\Filter\Components\Paginator;
use WebChemistry\Filter\DataSource\IDataSource;

class FilterFacade {

	/** @var Filter */
	private $filter;

	/** @var IDataSource */
	private $dataSource;

	/** @var FilterValues */
	private $values;

	public function __construct(Filter $filter, IDataSource $dataSource, FilterValues $values) {
		$this->filter = $filter;
		$this->dataSource = $dataSource;
		$this->values = $values;
	}

	public function getShowFrom(): int {
		$from = (int) $this->values->getLimitPerPage() * ($this->getPage() - 1) + 1;

		return $from < $this->getItemCount() ? $from : $this->getItemCount();
	}

	public function getShowTo(): int {
		if ($this->values->getLimitPerPage() === null) {
			return $this->getItemCount();
		}

		$to = $this->values->getLimitPerPage() * $this->getPage();

		return $to < $this->getItemCount() ? $to : $this->getItemCount();
	}

	public function isFirstPage(): bool {
		return $this->getPaginator()->isFirstPage();
	}

	public function getItemCount(): int {
		return $this->dataSource->getItemCount();
	}

	public function getPage(): int {
		return $this->getPaginator()->getPage();
	}

	public function getItemsOnPage(): int {
		return $this->getPaginator()->getItemsOnPage();
	}

	public function hasData(): bool {
		$data = $this->filter->getData();
		if ($data instanceof \Countable) {
			return (bool) $data->count();
		}

		return (bool) $data;
	}

	public function getData(): iterable {
		return $this->filter->getData();
	}

	public function getPaginator(): Paginator {
		return $this->filter['paginator'];
	}

	public function getForms(): Container {
		return $this->filter['forms'];
	}

	public function getSearch(): Form {
		return $this->filter['search'];
	}

	public function getResetLink(): string {
		return $this->filter->getResetLink();
	}

	public function getLink(string $name, $val): string {
		return $this->filter->link('link!', [$name, $val]);
	}

	public function getOrder(): Form {
		return $this->filter['order'];
	}

	public function getLimitPerPage(): Form {
		return $this->filter['limitPerPage'];
	}

	public function __get(string $name) {
		$getter = 'get' . ucfirst($name);
		if (method_exists($this, $getter)) {
			return $this->$getter();
		}

		ObjectHelpers::strictGet(get_class($this), $name);
	}

}
