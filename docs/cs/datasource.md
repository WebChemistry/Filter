# DataSource

**Akceptují:** Nette\Database\Table\Selection, Doctrine\ORM\QueryBuilder

## Použití v presenteru

```php

class BasePresenter extends Nette\Application\UI\Presenter {

    protected function createComponentFilter() {
        $settings = new WebChemistry\Filter\Settings;
        
        $settings->setDataSource(function () {
            return $this->db->table('user');
        });
        
        // Nebo
        
        $settings->dataSource = $this->db->table('user');
        
        return $settings->createFilter();
    }
}
```

## Použití v komponentě

```php

class Filter extends WebChemistry\Filter\FilterComponent {
    
    /** @var Nette\Database\Context */
    private $db;
    
    public function __construct(Nette\Database\Context $db) {
        $this->db = $db;
    }
    
    public function startup(WebChemistry\Filter\Settings $settings) {
        $settings->setDataSource(function () {
            return $this->db->table('user');
        });
        
        // Nebo
        
        $settings->dataSource = $this->db->table('user');
    }
}

```

## Použití v šabloně

```html
{var $filter = $presenter['filter']}

{foreach $filter->data as $row}
    <div>{$row->name}</div>
{/foreach}
```