## Basic usage

Component class:
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
	    
	    $settings->getPaginator()->setLimit(10);
	    $settings->getDataSource()->setCallback([$this, 'dataSource']);
	}
	
	public function dataSource(array $filterData) {
		$table = $this->context->table('test');

		return $table;
	}
}
```

Register to neon as service:
```yaml
services:
    - App\DemoFilter
```

use in presenter:
```php
class HomepagePresenter extends BasePresenter {

    /** @var App\DemoFilter @inject */
    public $demoFilter;

    protected function createComponentDemoFilter() {
        return $this->demoFilter;
    }

}
```

and render it in template:

```html
{filter demoFilter}
    {foreach $filter->getData() as $item}
        {$item->name}
    {/foreach}
    
    {control $filter->pagintator}
{/filter}
```
