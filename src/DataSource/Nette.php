<?php

namespace WebChemistry\Filter\DataSource;

use Nette\ComponentModel\Component;
use Nette\Database\Table\Selection;
use WebChemistry\Filter\Settings;

/**
 * @property-read Selection $source
 * @property-read int $currentCount
 * @property-read mixed $data
 * @property-read int $count
 * @property int $limit
 * @property-write string $select
 */
class Nette extends Component implements IDataSource {

	/** @var Selection */
	private $source;

	/** @var integer */
	private $count = NULL;

	/** @var \WebChemistry\Filter\Settings */
	private $settings;

	public function __construct($source, Settings $settings) {
		$this->source = $source;
		$this->settings = $settings;
	}

	/************************* Setters **********************/

	public function setSelect($select) {
		$this->source->select($select);

		return $this;
	}

	/************************* Getters **********************/

	/**
	 * @return Selection
	 */
	public function getSource() {
		return $this->source;
	}

	public function getCount() {
		if ($this->count === NULL) {
			$column = !$this->source->getPrimary(FALSE) ? '*' : $this->source->getPrimary();

			$this->count = $this->source->count($column);
		}

		return $this->count;
	}

	public function getData() {
		if ($this->settings->getLimit() !== NULL) {
			$this->source->limit($this->settings->getLimit(), $this->settings->getOffset());
		}

		return $this->source->fetchAll();
	}
}
