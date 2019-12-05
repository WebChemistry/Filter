<?php

declare(strict_types=1);

namespace WebChemistry\Filter;

use Nette\Application\UI\Component;

/**
 * Internal use FilterValues
 */
final class FilterOptions {

	/** @var int|null */
	public $limitPerPage = 10;
	
	/** @var bool[] name => true */
	public $links = [];
	
	/** @var callable[] */
	public $forms = [];
	
	/** @var array */
	public $snippets = [];
	
	/** @var callable */
	public $source;

	/** @var array */
	public $sourceOptions = [];

	/** @var string|null */
	public $paginatorFile;

	/** @var bool */
	public $ajax = false;

	/** @var bool */
	public $append = false;

	/** @var string[] name => type */
	public $types = [];

	/** @var array name => defaultValue */
	public $defaults = [];

	/** @var array name => label */
	public $order = [];

	/** @var array name => values */
	public $orderValues = [];

	/** @var callable[] */
	public $onAjax = [];

	/** @var array */
	public $limits = [10 => 10, 20 => 20, 50 => 50, 100 => 100];

	public function callAjax(Component $component): void {
		foreach ($this->onAjax as $callback) {
			$callback($component);
		}
	}

	public function addLink(string $name): void {
		$this->links[$name] = true;
	}

	public function addForm(callable $callback, string $name): void {
		$this->forms[$name] = $callback;
	}

	public function addDefaultValue(string $name, $default): void {
		$this->defaults[$name] = $default;
	}

	public function addType(string $name, string $type): void {
		$this->types[$name] = $type;
	}

}