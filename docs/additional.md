## Additional data for each row

## Component class

```php
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
	    $this->createLinks();
	    $this->createAdditional(); // important
	    
	    $settings->getPaginator()->setLimit(10);
	    $settings->getDataSource()->setCallback([$this, 'dataSource']);
	    $settings->setDefaultFilterData([
	        'name' => NULL,
	        'category'
	    ]);
	}
	
	/**
	 * important
	 */
	private function createAdditional() {
	    $additional = $this->setttings->getAdditional();
	    
	    $additional->add(function ($item) {
	        $result = $this->context->table('items')->select('SUM(value) as sum')->where('user = ?', $item->id)->fetch();
	        
	        return $result['sum'];
	    }, 'itemSum');
	}
	
	private function createLinks() {
	    $links = $this->settings->getLinks();
	    
	    $links->add('category');
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
		
		foreach (['name' => 'like', 'category' => 'is'] as $name => $type) {
        	if (!$filterData[$name]) {                                                         
        		continue;                                                                      
        	}                                                                                  
        	if ($type === 'like') {                                                            
        		$where["$name LIKE ?"] = '%' . $filterData[$name] . '%';                       
        	} else if ($type === 'is') {                                                                           
        	}                                                                                  
        		$where["$name = ?"] = $filterData[$name];                                      
        }                                                                                      
        if (isset($where)) {                                                                   
        	$table->where($where);                                                             
        }                                                                                      

		return $table;
	}
}
```
## Template

```html
{block aside}
    {filter demoFilter}
        {foreach $categories as $id => $name}
            <a href="$filter->dynamicLink('category', $id)">{$name}</a>
        {/foreach}
    {/filter}
{/block}

{block content}
    {filter demoFilter}
        {control $filter['filterForm']}
    
        {foreach $filter->getData() as $item}
            {$item->name}
            Item sum: {$filter->additional->itemSum} {* important *}
        {/foreach}
        
        {control $filter->pagintator}
    {/filter}
{/block}
```
