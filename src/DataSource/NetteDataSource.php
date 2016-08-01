<?php

namespace WebChemistry\Filter\DataSource;

use Nette\ComponentModel\Component;
use Nette\Database\Table\Selection;

/**
 * @property-read Selection $source
 * @property-read int $currentCount
 * @property-read mixed $data
 * @property-read int $count
 * @property int $limit
 * @property-write string $select
 */
class NetteDataSource extends Component implements IDataSource {

	/** @var Selection */
	private $source;

	/** @var integer */
	private $count = NULL;

	/**
	 * @param Selection $source
	 */
	public function __construct($source) {
		$this->source = $source;
	}

	/************************* Setters **********************/

	/**
	 * @param string $select
	 * @return self
	 */
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

	/**
	 * @return int
	 */
	public function getCount() {
		if ($this->count === NULL) {
			$column = !$this->source->getPrimary(FALSE) ? '*' : $this->source->getPrimary();

			$this->count = $this->source->count($column);
		}

		return $this->count;
	}

	/**
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getData($limit = NULL, $offset = NULL) {
		if ($limit !== NULL) {
			$this->source->limit($limit, $offset);
		}

		return $this->source->fetchAll();
	}

}
