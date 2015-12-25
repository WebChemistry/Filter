<?php

namespace WebChemistry\Filter;

use Nette\ComponentModel\Container;
use Nette;
use Nette\ComponentModel\IComponent;

class ComponentForm extends Container implements \ArrayAccess {

	/** @var \WebChemistry\Filter\Settings */
	private $settings;

	public function __construct(Settings $settings) {
		$this->monitor('WebChemistry\Filter\FilterComponent');
		$this->settings = $settings;
	}

	public function addComponent(IComponent $component, $name, $insertBefore = NULL) {
		if (!$component instanceof Nette\Forms\Form) {
			throw new Exception(printf('Form must be instance of Nette\Forms\Form, %s given.', get_class($component)));
		}

		return parent::addComponent($component, $name, $insertBefore);
	}

	protected function attached($obj) {
		parent::attached($obj);

		if ($obj instanceof FilterComponent) {
			/** @var Nette\Forms\Form $cmp */
			foreach ($this->getComponents() as $cmp) {
				if ($this->settings->isAjaxForm() && $this->settings->getSnippet()) {
					if (is_string($cmp->getElementPrototype()->class)) {
						$cmp->getElementPrototype()->class .= ' ajax';
					} else {
						$cmp->getElementPrototype()->class[] = 'ajax';
					}
				}

				$cmp->onSuccess[] = [$obj, 'successForm'];
			}
		}
	}

	/**
	 * Adds the component to the container.
	 * @param  string  component name
	 * @param  Nette\ComponentModel\IComponent
	 * @return void
	 */
	public function offsetSet($name, $component) {
		$this->addComponent($component, $name);
	}


	/**
	 * Returns component specified by name. Throws exception if component doesn't exist.
	 * @param  string  component name
	 * @return Nette\ComponentModel\IComponent
	 * @throws Nette\InvalidArgumentException
	 */
	public function offsetGet($name) {
		return $this->getComponent($name, TRUE);
	}


	/**
	 * Does component specified by name exists?
	 * @param  string  component name
	 * @return bool
	 */
	public function offsetExists($name) {
		return $this->getComponent($name, FALSE) !== NULL;
	}


	/**
	 * Removes component from the container.
	 * @param  string  component name
	 * @return void
	 */
	public function offsetUnset($name) {
		$component = $this->getComponent($name, FALSE);
		if ($component !== NULL) {
			$this->removeComponent($component);
		}
	}
}