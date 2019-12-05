<?php declare(strict_types = 1);

namespace WebChemistry\Filter\DataSource;

use WebChemistry\Filter\FilterOptions;

interface IDataSourceFactory {

	public function create($source, array $options): IDataSource;

}
