<?php

declare(strict_types=1);

namespace WebChemistry\Filter\Components;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\TextInput;
use WebChemistry\Filter\FilterOptions;
use WebChemistry\Filter\FilterParameters;
use WebChemistry\Filter\FilterValues;

final class FormBuilder {

	/** @var Form */
	private $form;

	/** @var bool */
	private $init = false;

	/** @var FilterOptions */
	private $options;

	/** @var FilterParameters */
	private $parameters;

	public function __construct(FilterOptions $options, FilterParameters $parameters) {
		$this->form = new Form();
		$this->options = $options;
		$this->parameters = $parameters;
	}

	private function createBase(string $name, ?string $label, $default = null): TextInput {
		$input = $this->form->addText($name, $label)
			->setNullable();

		$this->options->addDefaultValue($name, $default);

		return $input;
	}

	public function addInt(string $name, ?string $label, ?int $default = null): TextInput {
		$input = $this->createBase($name, $label, $default)->setHtmlType('number');

		$this->options->addType($name, 'int');

		return $input;
	}

	public function addSelect(string $name, ?string $label, array $items, $default = null): SelectBox {
		return $this->addSelectCustom($name, $label, $items, 'mixed', $default);
	}

	public function addSelectCustom(string $name, ?string $label, array $items, string $type, $default = null): SelectBox {
		$input = $this->form->addSelect($name, $label, $items);
		$input->checkDefaultValue(false);

		$this->options->addDefaultValue($name, $default);
		$this->options->addType($name, $type);

		return $input;
	}

	public function addSelectString(string $name, ?string $label, array $items, ?string $default = null): SelectBox {
		return $this->addSelectCustom($name, $label, $items, 'string', $default);
	}

	public function addSelectInt(string $name, ?string $label, array $items, ?int $default = null): SelectBox {
		return $this->addSelectCustom($name, $label, $items, 'int', $default);
	}

	public function addCheckbox(string $name, ?string $label, ?bool $default = null): Checkbox {
		$input = $this->form->addCheckbox($name, $label);

		$this->options->addDefaultValue($name, $default);
		$this->options->addType($name, 'bool');

		return $input;
	}

	public function addString(string $name, ?string $label, ?string $default = null): TextInput {
		$input = $this->createBase($name, $label, $default);

		$this->options->addType($name, 'string');

		return $input;
	}

	public function getForm(): Form {
		return $this->form;
	}

}
