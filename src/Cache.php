<?php

namespace WebChemistry\Filter;

use Latte\IMacro;
use Latte\MacroNode;
use Latte\PhpWriter;
use Nette\Bridges\CacheLatte\CacheMacro;
use Nette\Caching\IStorage;
use Nette\Caching\OutputHelper;
use Nette\Utils\Random;

class Cache extends CacheMacro implements IMacro {

	/**
	 * New node is found.
	 *
	 * @return bool
	 */
	public function nodeOpened(MacroNode $node) {
		parent::nodeOpened($node);

		$node->openingCode = PhpWriter::using($node)
			->write('<?php if (WebChemistry\Filter\Cache::createCache($netteCacheStorage, %var, $_g->caches, %node.array?)) { ?>',
				Random::generate()
			);
	}

	/**
	 * Starts the output cache. Returns Nette\Caching\OutputHelper object if buffering was started.
	 *
	 * @param IStorage $cacheStorage
	 * @param string $key
	 * @param OutputHelper[] $parents
	 * @param array $args
 	 * @return OutputHelper
	 */
	public static function createCache(IStorage $cacheStorage, $key, & $parents, array $args = NULL) {
		if (count($args[0]) && array_key_exists(0, $args) && is_array($args[0])) {
			$args = $args[0];
		}
		return parent::createCache($cacheStorage, $key, $parents, $args);
	}

}
