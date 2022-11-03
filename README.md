# History Bundle

This bundle add History management for entities.

## Requirements
Symfony ``^5.4``

PHP ``^8.1``

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
# config/packages/doctrine.yaml
doctrine:
  orm:
    resolve_target_entities:
      Atournayre\Bundle\HistoriqueBundle\Interfaces\History: App\Model\History
      Symfony\Component\Security\Core\User\UserInterface: App\Model\User

# config/packages/atournayre_historique.yaml
atournayre_historique:
  history_class: App\Model\History
```

Add **History** entity to your application.
```php
<?php

namespace App\Model;

use Atournayre\Bundle\HistoriqueBundle\Entity\History as BaseHistory;
use Atournayre\Bundle\HistoriqueBundle\EventSubscriber\HistoryEventSubscriber;
use Atournayre\Bundle\HistoriqueBundle\Interfaces\History as HistoryInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\EntityListeners([HistoryEventSubscriber::class])]
class History extends BaseHistory implements HistoryInterface
{
    // It is recommended not to extend this entity!
}
```

## Usage

### Entities
You can add **History** to existing entities or add it to new ones.

To add history to an entity, it needs to implements ``HistorycableInterface``.

Then use ``HistorycableTrait`` to add a relation between **YourEntity** and **History**. 
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

### Factories
Factories are where magic happens.

You have to create a Factory that implements how you want to store the history.

```php
<?php

namespace App\Factory;

use App\Entity\Utilisateur;
use Atournayre\Bundle\HistoriqueBundle\DTO\HistoryDTO;
use Atournayre\Bundle\HistoriqueBundle\Exception\EmptyChangeSetException;
use Atournayre\Bundle\HistoriqueBundle\Factory\AbstractFactory;
use Atournayre\Bundle\HistoriqueBundle\Interfaces\History;use Symfony\Component\Security\Core\User\UserInterface;

class YourEntityFactory extends AbstractFactory
{
    /**
     * @throws EmptyChangeSetException
     */
    public function create(array $changeSet): History
    {
        // Create how many methods you want for each node in your change set.
        $this->user($changeSet);
        // You must call this method (it will convert $changeSet and create the History entity).
        return parent::createHistory();
    }

    // This method implement how information are stored when a user is changed.
    private function user(array $changeSet): void
    {
        /** @var UserInterface[]|null $currentChangeSet */
        $currentChangeSet = $changeSet['user'] ?? null;

        if (is_null($currentChangeSet)) return;

        $this->changeSet->set('user', new HistoryDTO(
            'New username',
            $currentChangeSet[0]?->getUsername(),
            $currentChangeSet[1]?->getUsername(),
        ));
    }
}

```

Map Entity to Factory

Once you factory is created, you need to add a mapping to the config file, so the listener can automatically get the right factory for the right entity.
```yaml
# config.packages/atournayre_historique.yaml
atournayre_historique:
  mappings:
    'App\Entity\YourEntity': App\Factory\YourEntityFactory
```
With this, you can locate entities and factories anywhere in your project.

### How to get values ?

> ⚠️ Since version 3
> 
> This methods can be obsolete.
> 
> Update coming soon. Feel free to create issues.

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
