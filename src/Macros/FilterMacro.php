<?php

namespace WebChemistry\Filter\Macros;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

class FilterMacro extends MacroSet {

	public static function install(Compiler $compiler) {
		$self = new static($compiler);

		$self->addMacro('filter', [$self, 'macroFilter'], [$self, 'macroFilterEnd']);
	}

	public function macroFilter(MacroNode $macroNode, PhpWriter $phpWriter) {
		return $phpWriter->write('if (isset($filter)) { $_tmpFilter = $filter; } $filter = $control[%node.word]->createFacade();');
	}

	public function macroFilterEnd(MacroNode $macroNode, PhpWriter $phpWriter) {
		return $phpWriter->write('if (isset($_tmpFilter)) { $filter = $_tmpFilter; unset($_tmpFilter); }');
	}

}
