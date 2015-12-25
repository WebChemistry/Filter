<?php

namespace WebChemistry\Filter\DataSource;

use WebChemistry\Filter\Settings;

interface IDataSource {

	public function __construct($source, Settings $settings);

	public function getData();

	public function getCount();

	public function setSelect($select);
}