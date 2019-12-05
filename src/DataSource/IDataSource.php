<?php

declare(strict_types=1);

namespace WebChemistry\Filter\DataSource;

interface IDataSource {

	public function getItemCount(): int;

	public function getData(?int $limit, ?int $offset): iterable;

}
