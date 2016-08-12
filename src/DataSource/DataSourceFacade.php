<?php

namespace WebChemistry\Filter\DataSource;

use Doctrine\ORM\QueryBuilder;
use Nette\Database\Table\Selection;
use WebChemistry\Filter\FilterException;

class DataSourceFacade {

	/** @var callable */
	private $callback;

	/** @var IDataSource */
	private $dataSource;

	/** @var callable[] */
	public $onCreate = [];

	/**
	 * @param callable $callback
	 */
	public function setCallback(callable $callback) {
		$this->callback = $callback;
	}

	/**
	 * @return callable
	 */
	public function getCallback() {
		return $this->callback;
	}

	/**
	 * @param array $filterData
	 * @throws FilterException
	 * @return DoctrineDataSource|IDataSource|NetteDataSource
	 */
	public function __callDataSource(array $filterData) {
		if (!$this->dataSource) {
			if (!$this->callback) {
				throw new FilterException('DataSource is not set, please use $settings->getDataSource()->setCallback(...).');
			}
			$dataSource = call_user_func($this->callback, $filterData);
			if ($dataSource instanceof Selection) {
				$this->dataSource = new NetteDataSource($dataSource);
			} else if ($dataSource instanceof QueryBuilder) {
				$this->dataSource = new DoctrineDataSource($dataSource);
			} else {
				throw new FilterException('Bad datasource.');
			}

			foreach ($this->onCreate as $callback) {
				$callback($dataSource);
			}
		}

		return $this->dataSource;
	}

	/**
	 * @return IDataSource|null
	 */
	public function getDataSource() {
		return $this->dataSource;
	}

	/**
	 * @throws FilterException
	 */
	public function check() {
		if (!$this->callback) {
			throw new FilterException('DataSource is not set, please use $settings->getDataSource()->setCallback(...).');
		}
		if (!$this->dataSource) {
			throw new FilterException('DataSource is not called.');
		}
	}

}
