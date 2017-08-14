<?php

declare(strict_types=1);

namespace WebChemistry\Filter;

/**
 * Internal use FilterValues
 */
final class FilterOptions {

	/** @var int|null */
	public $limitPerPage;
	
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
	public $ajax;

	/** @var string[] name => type */
	public $types = [];

	/** @var array name => defaultValue */
	public $defaults = [];

	/** @var array name => label */
	public $order = [];

	/** @var array name => values */
	public $orderValues = [];

	/** @var array */
	public $limits = [10 => 10, 20 => 20, 50 => 50, 100 => 100];

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