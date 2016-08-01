<?php

namespace WebChemistry\Filter\DataSource;

use Doctrine as Doc, Nette;

use Doctrine\ORM\QueryBuilder;

/**
 * @property-read QueryBuilder $queryBuilder
 * @property-read int $currentCount
 * @property-read mixed $data
 * @property-read int $count
 * @property int $limit
 * @property-write string $select
 */
class DoctrineDataSource extends Nette\ComponentModel\Component implements IDataSource {

	/** @var QueryBuilder */
	private $builder;

	/** @var integer */
	private $count = NULL;

	/**
	 * @param mixed $source
	 */
	public function __construct($source) {
		$this->builder = $source;
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

	/**
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getData($limit = NULL, $offset = NULL) {
		if ($limit !== NULL) {
			$this->builder->setMaxResults($limit);
			$this->builder->setFirstResult($offset);
		}

		return $this->builder->getQuery()->getResult();
	}

	public function getCount() {
		if ($this->count === NULL) {
			$alias = $this->builder->getRootAliases();
			$builder = clone $this->builder;
			$result = $builder->select('COUNT(' . current($alias) . '.id) AS count')->getQuery()->getSingleResult();
			$this->count = $result['count'];
		}

		return $this->count;
	}

}
