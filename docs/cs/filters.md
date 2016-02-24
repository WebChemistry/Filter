# Filters

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
        }, 'search'); // První formulář
        
        $settings->cacheFiltering = TRUE; // Zapne cachování s filtry
        $settings->cacheArgs = [
            Nette\Caching\Cache::TAGS => ['tag']
        ]; // Nastavení cache argumentů
        
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
        
        $settings->cacheFiltering = TRUE; // Zapne cachování s filtry
        $settings->cacheArgs = [
            Nette\Caching\Cache::TAGS => ['tag']
        ]; // Nastavení cache argumentů
    }
}

```

## Použití v šabloně

```html
{var $filter = $presenter['filter']}

{control $filter['forms']['search']}

{cache $filter->getCache()}
    {foreach $filter->data as $row}
        <div>{$row->name}</div>
    {/foreach}
    
    {control $filter['paginator']}
{/cache}
```