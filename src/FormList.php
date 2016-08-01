<?php

namespace WebChemistry\Filter;

use Nette\ComponentModel\Container;
use Nette\ComponentModel\IComponent;
use Nette\Forms\Form;
use Nette\InvalidArgumentException;

class FormList extends Container {

	/** @var bool */
	private $ajax = FALSE;

	public function __construct() {
		$this->monitor(FilterComponent::class);
	}

	/**
	 * @param callable $callback
	 * @param string $name
	 * @return self
	 */
	public function add(callable $callback, $name) {
		$form = $callback();
		$this->addComponent($form, $name);

		return $this;
	}

	/**
	 * @param bool $ajax
	 * @return self
	 */
	public function setAjax($ajax = TRUE) {
		$this->ajax = $ajax;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAjax() {
		return $this->ajax;
	}

	/**
	 * @param IComponent $component
	 * @param string $name
	 * @param IComponent $insertBefore
	 * @return Container
	 * @throws InvalidArgumentException
	 */
	public function addComponent(IComponent $component, $name, $insertBefore = NULL) {
		if (!$component instanceof Form) {
			throw new InvalidArgumentException(printf('Form must be instance of Nette\Forms\Form, %s given.', get_class($component)));
		}

		return parent::addComponent($component, $name, $insertBefore);
	}

	protected function attached($obj) {
		parent::attached($obj);

		if ($obj instanceof FilterComponent) {
			/** @var Form $cmp */
			foreach ($this->getComponents() as $cmp) {
				if ($this->isAjax()) {
					$cmp->getElementPrototype()->appendAttribute('class', 'ajax');
				}

				$cmp->onSuccess[] = [$obj, 'successForm'];
				$cmp->setDefaults($obj->getFilterValues());
			}
		}
	}

}
