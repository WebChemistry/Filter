<?php

namespace WebChemistry\Filter;

class Filter extends FilterComponent {

	public function __construct(Settings $settings) {
		$this->settings = $settings;
	}

	protected function startup(Settings $settings) {}

}