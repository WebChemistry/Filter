<?php

namespace WebChemistry\Filter\DataSource;

interface IDataSource {

	/**
	 * @param mixed $source
	 */
	public function __construct($source);

	/**
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getData($limit = NULL, $offset = NULL);

	/**
	 * @return int
	 */
	public function getCount();

	/**
	 * @param string $select
	 * @return mixed
	 */
	public function setSelect($select);

}
