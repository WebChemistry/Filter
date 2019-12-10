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

	/** @var bool|null */
	private $compositeId = null;

	public function __construct(QueryBuilder $queryBuilder, array $options = []) {
		if (isset($options['hydrationMode'])) {
			$this->resultType = $options['hydrationMode'];
		}

		$this->queryBuilder = $queryBuilder;
		$this->options = $options;
	}

	protected function isCompositeId(): bool {
		if ($this->compositeId === null) {
			$this->compositeId = false;
			foreach ($this->queryBuilder->getRootEntities() as $entity) {
				if ($this->queryBuilder->getEntityManager()->getClassMetadata($entity)->isIdentifierComposite) {
					$this->compositeId = true;

					break;
				}
			}
		}

		return $this->compositeId;
	}

	public function getItemCount(): int {
		if ($this->count === NULL) {
			$paginator = new Paginator($this->queryBuilder, !$this->isCompositeId());

			$this->count = $paginator->count();
		}

		return $this->count;
	}

	public function getData(?int $limit, ?int $offset): iterable {
		if ($limit !== NULL) {
			$this->queryBuilder->setFirstResult($offset);
			$this->queryBuilder->setMaxResults($limit);

			$paginator = new Paginator($this->queryBuilder->getQuery()->setHydrationMode($this->resultType), !$this->isCompositeId());

			return iterator_to_array($paginator->getIterator());
		}

		return $this->queryBuilder->getQuery()->getResult($this->resultType);
	}

}
