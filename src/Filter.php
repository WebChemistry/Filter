<?php

namespace WebChemistry\Filter;

abstract class BaseFilterComponent extends FilterComponent {

	/**
	 * @return int
	 */
	public function getItemCount() {
		$this->init();

		return $this->settings->getItemCount() < $this->settings->getLimit() ? $this->settings->getLimit() : $this->settings->getItemCount();
	}

	/**
	 * @return int
	 */
	public function getLimit() {
		return $this->settings->getLimit();
	}

	/**
	 * @return int
	 */
	public function getPage() {
		return $this->getPaginator()->page;
	}

}
