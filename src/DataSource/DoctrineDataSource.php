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

	/** @var array */
	private $options = [
		'hydrationMode' => AbstractQuery::HYDRATE_OBJECT,
		'outputWalkers' => true,
	];

	/** @var bool|null */
	private $compositeId = null;

	/** @var Paginator|null */
	private $paginator;

	public function __construct(QueryBuilder $queryBuilder, array $options = []) {
		$this->queryBuilder = $queryBuilder;

		$this->options = array_replace($this->options, $options);
	}

	protected function getPaginator(): Paginator {
		if (!$this->paginator) {
			$query = $this->queryBuilder->getQuery()->setHydrationMode($this->options['hydrationMode']);

			$this->paginator = new Paginator($query, !$this->isCompositeId());
			$this->paginator->setUseOutputWalkers($this->options['outputWalkers']);
		}

		return $this->paginator;
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
			$this->count = $this->getPaginator()->count();
		}

		return $this->count;
	}

	public function getData(?int $limit, ?int $offset): iterable {
		$query = $this->getPaginator()->getQuery();
		$query->setMaxResults($limit);
		$query->setFirstResult($offset);

		return iterator_to_array($this->getPaginator()->getIterator());
	}

}
