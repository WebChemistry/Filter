<?php declare(strict_types = 1);

namespace WebChemistry\Filter\DI;

use Nette\DI\CompilerExtension;
use WebChemistry\Filter\DataSource\DataSourceRegistry;
use WebChemistry\Filter\FilterBuilderFactory;
use WebChemistry\Filter\IFilterBuilderFactory;

/**
 * @internal
 */
final class FilterExtension extends CompilerExtension {

	/** @var array */
	public $defaults = [
		'filterBuilderFactory' => FilterBuilderFactory::class,
		'paginator' => [
			'template' => null,
		],
	];

	public function loadConfiguration() {
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);

		$builder->addDefinition($this->prefix('dataSourceRegistry'))
			->setType(DataSourceRegistry::class);

		$filter = $builder->addFactoryDefinition($this->prefix('filterBuilderFactory'))
			->setImplement(IFilterBuilderFactory::class)
			->getResultDefinition();

		if ($config['paginator']['template']) {
			$filter->addSetup('setPaginatorFile', [$config['paginator']['template']]);
		}
	}

}
