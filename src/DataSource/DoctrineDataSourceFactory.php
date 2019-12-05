<?php declare(strict_types = 1);

namespace WebChemistry\Filter\DataSource;

use WebChemistry\Filter\FilterOptions;

class DoctrineDataSourceFactory implements IDataSourceFactory {

	public function create($source, array $options): IDataSource {
		return new DoctrineDataSource($source, $options);
	}

}
