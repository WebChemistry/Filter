<?php

namespace WebChemistry\Filter\DI;

use Latte\Engine;
use Nette;
use Nette\DI\CompilerExtension;
use WebChemistry\Filter\Macros\FilterMacro;
use WebChemistry\Filter\Settings;

class FilterExtension extends CompilerExtension {

	/** @var array */
	public $defaults = [
		'paginatorTemplate' => NULL,
	];

	public function beforeCompile() {
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('latte.latteFactory')
			->addSetup('?->onCompile[] = function ($engine) { ?::install($engine->getCompiler()); }', ['@self', FilterMacro::class]);
	}

	public function afterCompile(Nette\PhpGenerator\ClassType $class) {
		$init = $class->getMethods()['initialize'];
		$config = $this->validateConfig($this->defaults);

		if ($config['paginatorTemplate']) {
			$init->addBody('?::$defaultPaginationTemplate = ?;', [Settings::class, $config['paginatorTemplate']]);
		}
	}

}
