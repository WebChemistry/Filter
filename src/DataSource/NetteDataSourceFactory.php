<?php declare(strict_types = 1);

namespace WebChemistry\Filter\DataSource;

use Nette\SmartObject;

final class NetteDataSourceFactory implements IDataSourceFactory {

	use SmartObject;
	
	public function create($source, array $options): IDataSource {
		return new NetteDataSource($source, $options);
	}
	
}
