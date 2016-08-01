# Form filters

## Component class

´´´php
namespace App;

use Nette\Database\Context;
use Nette\Application\UI\Form;
use WebChemistry\Filter\BaseFilterComponent;

class DemoFilter extends BaseFilterComponent {

	/** @var Context */
	private $context;

	public function __construct(Context $context) {
		$this->context = $context;
	}

	protected function startup() {
	    $settings = $this->settings;
	    
	    $this->createFormFilters(); // Important
	    
	    $settings->getPaginator()->setLimit(10);
	    $settings->getDataSource()->setCallback([$this, 'dataSource']);
	}
	
	/**
	 * important
	 */
	private function createFormFilters() {
	    $forms = $this->settings->getForms();
	    
	    $forms->add(function () {
	        $form = new Form();
	        
	        $form->addText('name', '̈́Filtering by name')
	            ->setRequired();
	            
	        $form->addSubmit('submit');
	        
	        return $form;
	    }, 'filterForm');
	}
	
	public function dataSource(array $filterData) {
		$table = $this->context->table('test');
		
		/** important **/
		if (isset($filterData['name'])) {
		    $table->where('name LIKE ?', '%' . $filterData['name'] . '%')
		}
		/** / important **/

		return $table;
	}
}
´´´

We can also use method ´setDefaultFilterData´. These values are used when form is not submitted.

´´´php
namespace App;

use Nette\Database\Context;
use Nette\Application\UI\Form;
use WebChemistry\Filter\BaseFilterComponent;

class DemoFilter extends BaseFilterComponent {

	/** @var Context */
	private $context;

	public function __construct(Context $context) {
		$this->context = $context;
	}

	protected function startup() {
	    $settings = $this->settings;
	    
	    $this->createFormFilters();
	    
	    $settings->getPaginator()->setLimit(10);
	    $settings->getDataSource()->setCallback([$this, 'dataSource']);
	    $settings->setDefaultFilterData([ // important
	        'name' => NULL
	    ]);
	}
	
	private function createFormFilters() {
	    $forms = $this->settings->getForms();
	    
	    $forms->add(function () {
	        $form = new Form();
	        
	        $form->addText('name', '̈́Filtering by name')
	            ->setRequired();
	            
	        $form->addSubmit('submit');
	        
	        return $form;
	    }, 'filterForm');
	}
	
	public function dataSource(array $filterData) {
		$table = $this->context->table('test');
		
		if ($filterData['name']) { // important
		    $table->where('name LIKE ?', '%' . $filterData['name'] . '%')
		}

		return $table;
	}
}
´´´
## Template

´´´html
{filter demoFilter}
    {control $filter['filterForm']} {* important *}

    {foreach $filter->getData() as $item}
        {$item->name}
    {/foreach}
    
    {control $filter->pagintator}
{/filter}
´´´
