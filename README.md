# History Bundle

This bundle add History management for entities.

## Requirements
Symfony ``^2.8``

PHP ``^5.6``

## Install
### Composer
```shell
composer require atournayre/historique-bundle
```
### Register bundle
```php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    // ...
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new \Atournayre\Bundle\HistoriqueBundle\HistoriqueBundle(),
            // ...
        );
    }
    // ...
}
```

## Configuration
Replace ``App\Model\History`` by your History entity.

Replace ``App\Model\User`` by your User entity.
```yaml
#app/config.config.yml
doctrine:
  orm:
    resolve_target_entities:
      Atournayre\Bundle\HistoriqueBundle\Interfaces\History: App\Model\History
      Symfony\Component\Security\Core\User\UserInterface: App\Model\User

atournayre_historique:
  history_class: App\Model\History
```

Add **History** entity to your application.
```php
<?php

namespace App\Model;

use Atournayre\Bundle\HistoriqueBundle\Entity\History as BaseHistory;
use Atournayre\Bundle\HistoriqueBundle\Interfaces\History as HistoryInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\EntityListeners({"Atournayre\Bundle\HistoriqueBundle\EventSubscriber\HistoryEventSubscriber"})
 */
class History extends BaseHistory implements HistoryInterface
{
    // You don't need to extend this entity.
}
```

## Usage
You can add **History** to existing entities or add it to new ones.

To add history to an entity, it needs to implements ``HistorycableInterface``.

Then use ``HistorycableTrait`` to adda relation between **YourEntity** and **History**. 
```php
<?php

use Atournayre\Bundle\HistoriqueBundle\Traits\HistorycableInterface;
use Atournayre\Bundle\HistoriqueBundle\Traits\HistorycableTrait;

class YourEntity implements HistorycableInterface
{
    //...
    use HistorycableTrait;
    //...
}
```

### How to get values ?
```php
use Doctrine\Common\Collections\Criteria;

$yourEntity = ...

// Get all the history (the most recent first) 
$allPreviousValues = $yourEntity->getAllPreviousValues();
$allPreviousValues = $yourEntity->getAllPreviousValues(Criteria::DESC);

// Get all the history (the most ancient first) 
$allPreviousValues = $yourEntity->getAllPreviousValues(Criteria::ASC);

// Assuming YourEntity as "title" property
// Get all the previous values from the history only for "title" property (the most recent first) 
$allPreviousTitles = $yourEntity->getAllPreviousValuesByName('title');
$allPreviousTitles = $yourEntity->getAllPreviousValuesByName('title', Criteria::DESC);

// Get all the previous values from the history only for "title" property (the most ancient first) 
$allPreviousTitles = $yourEntity->getAllPreviousValuesByName('title', Criteria::ASC);

// Get the last changes
$lastChanges = $yourEntity->getLastValues();

// Get the last "title"
$lastTitle = $yourEntity->getLastValuesByName('title');
```

## Contributing
Of course, open source is fueled by everyone's ability to give just a little bit
of their time for the greater good. If you'd like to see a feature or add some of
your *own* happy words, awesome! Tou can request it - but creating a pull request
is an even better way to get things done.

Either way, please feel comfortable submitting issues or pull requests: all contributions
and questions are warmly appreciated :).
