<?php

declare(strict_types=1);

namespace WebChemistry\Filter;

final class FilterValues {

	/** @var FilterOptions */
	private $options;

	/** @var FilterParameters */
	private $parameters;

	public function __construct(FilterOptions $options, FilterParameters $parameters) {
		$this->options = $options;
		$this->parameters = $parameters;
	}

	/**
	 * @return array
	 */
	public function getOrder(): ?array {
		$order = $this->parameters->order;
		$orders = $this->options->order;
		if ($order && isset($orders[$order])) {
			return $orders[$order];
		}
		if ($orders) {
			return current($orders);
		}

		return null;
	}

	public function hasFilter(string $name): bool {
		return isset($this->options->types[$name]);
	}

	public function getFilter(string $name) {
		if (!$this->hasFilter($name)) {
			throw new FilterException('Filter "' . $name . '" is not configured.');
		}
		if (isset($this->parameters->filters[$name])) {
			return $this->parameters->filters[$name];
		}

		return $this->options->defaults[$name];
	}

	public function getFilterValues($withDefaults = true): array {
		if ($withDefaults) {
			return $this->parameters->filters + $this->options->defaults;
		}

		return $this->parameters->filters;
	}

	public function getLimitPerPage(): ?int {
		if ($this->parameters->limitPerPage > 0) {
			return $this->parameters->limitPerPage;
		}

		return $this->options->limitPerPage;
	}

	public function getOrderKey(): ?string {
		return $this->parameters->order;
	}

	public function getSearchBoth(): string {
		return '%' . $this->parameters->search . '%';
	}

	public function getSearchLeft(): string {
		return '%' . $this->parameters->search;
	}

	public function getSearchRight(): string {
		return $this->parameters->search . '%';
	}

	public function getSearch(): ?string {
		return $this->parameters->search;
	}

	public function hasSearch(): bool {
		return $this->parameters->search !== null;
	}

}
