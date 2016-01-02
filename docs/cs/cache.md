# Nette cache

## Použití v presenteru

```php

class BasePresenter extends Nette\Application\UI\Presenter {

    protected function createComponentFilter() {
        $settings = new WebChemistry\Filter\Settings;
        
        $settings->setDataSource(function (array $filtering) {
            $query = $this->db->table('user');
            
            if ($filtering['search']) {
                $query->where('name LIKE ?', '%' . $filtering['search'] . '%');
            }
            
            return $query;
        });
        
        $settings->setLimit(10);
        
        $settings->setFilteringDefaults([
            'search' => NULL
        ]);
        
        $settings->addForm(function () {
            $form = new Nette\Application\UI\Form();

            $form->addText('search', 'Hledat podle jména');

            $form->addSubmit('send', 'Odeslat');

            return $form;
        }, 'search');
        
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
        $settings->setDataSource(function (array $filtering) {
            $query = $this->db->table('user');
            
            if ($filtering['search']) {
                $query->where('name LIKE ?', '%' . $filtering['search'] . '%');
            }
            
            return $query;
        });
        
        $settings->setLimit(10);
        
        $settings->setFilteringDefaults([
            'search' => NULL
        ]);
        
        $settings->addForm(function () {
            $form = new Nette\Application\UI\Form();

            $form->addText('search', 'Hledat podle jména');

            $form->addSubmit('send', 'Odeslat');

            return $form;
        }, 'search');
        
        
    }
}

```

## Použití v šabloně

```html
{var $filter = $presenter['filter']}

{control $filter['forms']['search']}
<div n:tag-if="$filter->useCache()" n:cache="$filter->cacheId tags => article">
	{foreach $filter->data as $row}
		<div>{$row->name}</div>
	{/foreach}

	{control $filter['paginator']}
</div>
```

# Filter cache

## Registrace neon

```yaml
services:
	nette.latteFactory:
		setup:
			- addMacro('cacheFilter', WebChemistry\Filter\Cache)
```

## Použití

```php

class Filter extends WebChemistry\Filter\FilterComponent {
    
    /** @var Nette\Database\Context */
    private $db;
    
    public function __construct(Nette\Database\Context $db) {
        $this->db = $db;
    }
    
    public function startup(WebChemistry\Filter\Settings $settings) {
        $settings->setDataSource(function (array $filtering) {
            $query = $this->db->table('user');
            
            if ($filtering['search']) {
                $query->where('name LIKE ?', '%' . $filtering['search'] . '%');
            }
            
            return $query;
        });
        
        $settings->setLimit(10);
        
        $settings->setFilteringDefaults([
            'search' => NULL
        ]);
        
        $settings->addForm(function () {
            $form = new Nette\Application\UI\Form();

            $form->addText('search', 'Hledat podle jména');

            $form->addSubmit('send', 'Odeslat');

            return $form;
        }, 'search');
        
        $settings->setCacheArgs(
        	Nette\Caching\Cache::TAGS => ['articles']
        );
    }
}

```

```html
{var $filter = $presenter['filter']}

{control $filter['forms']['search']}
<div n:cacheFilter="$filter->cache">
	{foreach $filter->data as $row}
		<div>{$row->name}</div>
	{/foreach}

	{control $filter['paginator']}
</div>
```