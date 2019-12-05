<?php

declare(strict_types=1);

namespace WebChemistry\Filter\DataSource;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DoctrineDataSource implements IDataSource {

	/** @var QueryBuilder */
	private $queryBuilder;

	/** @var int */
	private $count;

	/** @var int */
	private $resultType = AbstractQuery::HYDRATE_OBJECT;

	/** @var array */
	private $options;

	public function __construct(QueryBuilder $queryBuilder, array $options = []) {
		if (isset($options['hydrationMode'])) {
			$this->resultType = $options['hydrationMode'];
		}

		$this->queryBuilder = $queryBuilder;
		$this->options = $options;
	}

	public function getItemCount(): int {
		if ($this->count === NULL) {
			$paginator = new Paginator($this->queryBuilder);

			$this->count = $paginator->count();
			/*$alias = $this->queryBuilder->getRootAliases();
			$builder = clone $this->queryBuilder;
			$this->count = (int) $builder->select('count(' . current($alias) . '.id)')->getQuery()->getSingleScalarResult();*/
		}

		return $this->count;
	}

	public function getData(?int $limit, ?int $offset): iterable {
		if ($limit !== NULL) {
			$this->queryBuilder->setFirstResult($offset);
			$this->queryBuilder->setMaxResults($limit);

			$paginator = new Paginator($this->queryBuilder->getQuery()->setHydrationMode($this->resultType));

			return $paginator->getIterator();
		}

		return $this->queryBuilder->getQuery()->getResult($this->resultType);
	}

}
