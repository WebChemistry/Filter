<?php

namespace WebChemistry\Filter;

class Filter extends FilterComponent {

	/**
	 * @param Settings $settings
	 */
	public function __construct(Settings $settings) {
		$this->settings = $settings;
	}

	/**
	 * @param Settings $settings
	 * @return void
	 */
	protected function startup(Settings $settings) {}

}