<?php declare(strict_types = 1);

namespace WebChemistry\Filter\DataSource;

use Nette\Database\Table\Selection;
use Nette\SmartObject;

final class NetteDataSource implements IDataSource {

	use SmartObject;

	/** @var Selection */
	private $selection;

	/** @var mixed[] */
	private $options;

	public function __construct(Selection $selection, array $options) {
		$this->selection = $selection;
		$this->options = $options;
	}

	public function getItemCount(): int {
		return $this->selection->count();
	}

	public function getData(?int $limit, ?int $offset): iterable {
		$this->selection->limit($limit, $offset);

		return $this->selection->fetchAll();
	}

}
