<?php

namespace WebChemistry\Filter\DataSource;

use WebChemistry\Filter\Settings;

interface IDataSource {

	/**
	 * @param mixed $source
	 * @param Settings $settings
	 */
	public function __construct($source, Settings $settings);

	/**
	 * @return mixed
	 */
	public function getData();

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
