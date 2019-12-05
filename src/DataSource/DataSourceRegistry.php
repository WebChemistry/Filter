<?php declare(strict_types = 1);

namespace WebChemistry\Filter\DataSource;

use Doctrine\ORM\QueryBuilder;
use Nette\SmartObject;

class DataSourceRegistry {

	use SmartObject;

	/** @var IDataSourceFactory[] */
	private $registry = [];

	public function __construct() {
		$this->registry = [
			QueryBuilder::class => new DoctrineDataSourceFactory(),
		];
	}

	public function addFactory(string $typeOf, IDataSourceFactory $factory) {
		$this->registry[$typeOf] = $factory;

		return $this;
	}

	public function getDataSource($source, array $options = []): ?IDataSource {
		foreach ($this->registry as $type => $factory) {
			if ($source instanceof $type) {
				return $factory->create($source, $options);
			}
		}

		return null;
	}

}
