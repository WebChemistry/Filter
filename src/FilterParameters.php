<?php

declare(strict_types=1);

namespace WebChemistry\Filter;

use Nette\Application\UI\ComponentReflection;

/**
 * Internal use FilterValues
 */
final class FilterParameters {

	/** @var null|string */
	public $search;

	/** @var int|null */
	public $limitPerPage;

	/** @var string|null */
	public $order;

	/** @var array */
	public $filters = [];

	/** @var FilterOptions */
	private $options;

	public function __construct(FilterOptions $options) {
		$this->options = $options;
	}

	public function setSearch(?string $search): void {
		$this->search = $search;
	}

	public function setOrder(?string $order): void {
		$this->order = $order;
	}

	public function addLink(string $name, $value): void {
		if (!isset($this->options->links[$name])) {
			return;
		}

		$this->addFilter($name, $value);
	}

	/**
	 * @param array $filters
	 */
	public function setFilters(array $filters): void {
		foreach ($filters as $name => $val) {
			$this->addFilter($name, $val);
		}
	}

	public function setLimitPerPage(?int $limitPerPage): void {
		$this->limitPerPage = $limitPerPage;
	}

	public function addFilter(string $name, $value): void {
		if (isset($this->options->types[$name]) && $this->convertType($value, $name)) {
			$this->filters[$name] = $value;
		}
	}

	private function convertType(&$val, string $name): bool {
		if ($this->options->types[$name] === 'mixed') {
			return true;
		}

		return ComponentReflection::convertType($val, $this->options->types[$name]);
	}

	public function loadState(array $params): void {
		$this->filters = $params['filters'] ?? [];
		$this->search = $params['search'] ?? null;
		$this->order = $params['order'] ?? null;
		$this->limitPerPage = isset($params['limitPerPage']) ? (int) $params['limitPerPage'] : null;

		$this->checkFilters();
	}

	public function saveState(array &$params): void {
		if ($this->filters && !isset($params['filters'])) {
			$params['filters'] = $this->filters;
		}
		if ($this->search && !isset($params['search'])) {
			$params['search'] = $this->search;
		}
		if ($this->order && !isset($params['order'])) {
			$params['order'] = $this->order;
		}
		if ($this->limitPerPage && !isset($params['limitPerPage'])) {
			$params['limitPerPage'] = $this->limitPerPage;
		}
	}

	/**
	 * @return null|string
	 */
	public function getSearch(): ?string {
		return $this->search;
	}

	public function resetState(): void {
		$this->filters = [];
		$this->search = null;
		$this->order = null;
		$this->limitPerPage = null;
	}

	private function checkFilters(): void {
		foreach ($this->filters as $name => &$value) {
			if (!isset($this->options->types[$name]) || !$this->convertType($value, $name)) {
				unset($this->filters[$name]);
			}
		}
	}

}