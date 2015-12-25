<?php

namespace WebChemistry\Filter\DataSource;

use WebChemistry\DataFilter\AutoFilter;
use Doctrine as Doc, Nette;

use Doctrine\ORM\QueryBuilder;
use WebChemistry\Filter\Settings;

/**
 * @property-read QueryBuilder $queryBuilder
 * @property-read int $currentCount
 * @property-read mixed $data
 * @property-read int $count
 * @property int $limit
 * @property-write string $select
 */
class Doctrine extends Nette\ComponentModel\Component implements IDataSource {

	/** @var QueryBuilder */
	private $builder;

	/** @var Settings */
	private $settings;

	/** @var integer */
	private $count = NULL;

	public function __construct($source, Settings $settings) {
		$this->builder = $source;
		$this->settings = $settings;
	}

	/************************* Setters **********************/

	public function setSelect($select) {
		$this->builder->select($select);

		return $this;
	}

	/************************* Getters **********************/

	/**
	 * @return QueryBuilder
	 */
	public function getQueryBuilder() {
		return $this->builder;
	}

	public function getData() {
		if ($this->settings->getLimit() !== NULL) {
			$this->builder->setMaxResults($this->settings->getLimit());
			$this->builder->setFirstResult($this->settings->getOffset());
		}

		return $this->builder->getQuery()->getResult();
	}

	public function getCount() {
		if ($this->count === NULL) {
			$paginator = new Doc\ORM\Tools\Pagination\Paginator($this->builder);
			$this->count = $paginator->count();
		}

		return $this->count;
	}

}
