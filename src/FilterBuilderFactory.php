<?php declare(strict_types = 1);

namespace WebChemistry\Filter;

use Nette\Localization\ITranslator;
use WebChemistry\Filter\DataSource\DataSourceRegistry;

class FilterBuilderFactory implements IFilterBuilderFactory {

	/** @var DataSourceRegistry */
	private $registry;

	/** @var ITranslator|null */
	private $translator;

	public function __construct(DataSourceRegistry $registry, ?ITranslator $translator = null) {
		$this->translator = $translator;
		$this->registry = $registry;
	}

	public function create(): FilterBuilder {
		return new FilterBuilder($this->registry, $this->translator);
	}

}
