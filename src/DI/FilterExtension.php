<?php declare(strict_types = 1);

namespace WebChemistry\Filter\DI;

use Nette;
use Nette\DI\CompilerExtension;
use WebChemistry\Filter\DataSource\DataSourceRegistry;
use WebChemistry\Filter\FilterBuilderFactory;
use WebChemistry\Filter\IFilterBuilderFactory;

/**
 * @internal
 */
final class FilterExtension extends CompilerExtension {

	public function getConfigSchema(): Nette\Schema\Schema {
		return Nette\Schema\Expect::structure([
			'filterBuilderFactory' => Nette\Schema\Expect::string()->nullable(),
			'defaults' => Nette\Schema\Expect::structure([
				'ajax' => Nette\Schema\Expect::bool(false),
				'limitPerPage' => Nette\Schema\Expect::int(10),
				'paginatorFile' => Nette\Schema\Expect::string(),
			])
		]);
	}

	public function loadConfiguration() {
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();

		$builder->addDefinition($this->prefix('dataSourceRegistry'))
			->setType(DataSourceRegistry::class);

		$filter = $builder->addFactoryDefinition($this->prefix('filterBuilderFactory'))
			->setImplement(IFilterBuilderFactory::class)
			->getResultDefinition();

		if ($config->filterBuilderFactory) {
			$filter->setType($config->filterBuilderFactory);
		}

		foreach ($config->defaults as $name => $value) {
			$filter->addSetup('set' . ucfirst($name), [$value]);
		}
	}

}
