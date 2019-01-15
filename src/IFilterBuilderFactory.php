<?php declare(strict_types = 1);

namespace WebChemistry\Filter;

interface IFilterBuilderFactory {

	public function create(): FilterBuilder;

}
