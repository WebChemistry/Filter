<?php

declare(strict_types=1);

namespace WebChemistry\Filter\Components;

use Nette\ComponentModel\Container;
use Nette\ComponentModel\IComponent;

final class ComponentWrapper extends Container implements \ArrayAccess {

	public function offsetExists($name): bool {
		return (bool) $this->getComponent($name, false);
	}

	public function offsetGet($name): IComponent {
		return $this->getComponent($name);
	}

	public function offsetSet($name, $value): void {
		throw new \LogicException('offsetSet is not supported.');
	}

	public function offsetUnset($name): void {
		throw new \LogicException('offsetUnset is not supported');
	}

}
