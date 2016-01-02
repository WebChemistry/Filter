<?php

namespace WebChemistry\Filter;

use Latte;
use Latte\IMacro;
use Nette;
use Nette\Bridges\CacheLatte\CacheMacro;
use Nette\Utils\Random;

class Cache extends CacheMacro implements IMacro {

	/**
	 * New node is found.
	 *
	 * @return bool
	 */
	public function nodeOpened(Latte\MacroNode $node) {
		parent::nodeOpened($node);

		$node->openingCode = Latte\PhpWriter::using($node)
			->write('<?php if (WebChemistry\Filter\Cache::createCache($netteCacheStorage, %var, $_g->caches, %node.array?)) { ?>',
				Random::generate()
			);
	}

	/**
	 * Starts the output cache. Returns Nette\Caching\OutputHelper object if buffering was started.
	 *
	 * @param  Nette\Caching\IStorage
	 * @param  string
	 * @param  Nette\Caching\OutputHelper[]
	 * @param  array
	 * @return Nette\Caching\OutputHelper
	 */
	public static function createCache(Nette\Caching\IStorage $cacheStorage, $key, & $parents, array $args = NULL) {
		if (count($args[0]) && array_key_exists(0, $args) && is_array($args[0])) {
			$args = $args[0];
		}
		return parent::createCache($cacheStorage, $key, $parents, $args);
	}

}