# Event Sourcing for PHP
An Event Sourcing library for PHP.

## Installation
This library is available as [Composer](https://getcomposer.org) package:

```shell
composer require twanhaverkamp/event-sourcing-for-php
```

### Requirements
- PHP 8.2

> **Note:** Have a closer look at the example classes.
> These can be found in the _/Example_ directories.

---

## Components

### Aggregate root
An aggregate root class implements `AggregateRootInterface` (or extends `AbstractAggregateRoot`) and records all events.
Normally an object is instantiated through the magic `__construct` method, but since this is an event in itself
is this done through a static method (which best describes the event in its context).

**Constructor instantiation:**
```php
<?php

namespace MyApp\Entity;

final class User
{
    public function __construct(
        private string $emailAddress,
        private string $hashedPassword,
    ) {
    }

    public function setPassword(string $hashedPassword): void
    {
        $this->hashedPassword = $hashedPassword;
    }
}

// ...

use MyApp\Entity\User;

$user = new User('[email_address]', '[hashed_password]');
$user->setPassword('[new_hashed_password]');
```

**Static method instantiation:**
```php
<?php

namespace MyApp\AggregateRoot;

use MyApp\Event\UserWasRegisteredEvent;
use TwanHaverkamp\EventSourcingForPhp\AggregateRoot\AbstractAggregateRoot;
use TwanHaverkamp\EventSourcingForPhp\Uuid\Uuid;

final class User extends AbstractAggregateRoot
{
    public static function register(string $emailAddress, string $hashedPassword): self
    {
        $aggregateRoot = new self($aggregateRootId = Uuid::init());
        $aggregateRoot->recordEvent(new UserWasRegisteredEvent(
            $emailAddress,
            $hashedPassword,
            $aggregateRootId,
        ));

        return $aggregateRoot;
    }

    public function changePassword(string $newHashedPassword): void
    {
        $this->recordEvent(new UserPasswordWasChanged(
            $newHashedPassword,
            $this->getId(),
        ));
    }
}

// ...

use MyApp\AggregateRoot\User;

$user = User::register('[email_address]', '[hashed_password]');
$user->changePassword('[new_hashed_password]');
```

When the aggregate root is saved through its event store (as defined in the object's `getEventStore` method)
it can be turned into a projection (or data transfer object) with the `getProjection` method:
```php
<?php

use MyApp\EventStore\MyEventStore;

// ...

$this->myEventStore->save($user);

/** @var object $projection */
$projection = $user->getProjection();
```

Using the static `reconstitute` method, an aggregate root is rebuilt with the events from the event store.

### Events
An event class implements the `EventInterface` (or extends `AbstractEvent`) and holds event-related data
for an aggregate root. It's best practice to describe your events as if they took place in the past like:
[subject] + [verb] + [action] + [class-postfix]. The user registration then becomes `UserWasRegisteredEvent`.

**Important event methods:**
- `getAggregateRootId`: This establishes the relationship to the associated aggregate root.
- `getRecordedAt`: This determines the order in which the events of an aggregate root occurred.
- `getPayload`: This contains all relevant data that affect the aggregate root.

```php
<?php

namespace MyApp\Event;

use TwanHaverkamp\EventSourcingForPhp\Event\AbstractEvent;
use TwanHaverkamp\EventSourcingForPhp\Uuid\UuidInterface;

final class UserWasRegisteredEvent extends AbstractEvent
{
    public function __construct(
        private readonly string $emailAddress,
        private readonly string $hashedPassword,
        private readonly UuidInterface $aggregateRootId,
    ) {
        parent::__construct($aggregateRootId, new \DateTimeImmutable());
    }

    public function getPayload(): array{
        return [
            'emailAddress' => $this->emailAddress,
            'hashedPassword' => $this->hashedPassword,
        ];
    }
}
```

### Event store
An event store class implements the `EventStoreInterface` (or extends `AbstractEventStore`) and is responsible
for managing the events of an aggregate root.

If a project has multiple event stores, the `EventStoreManager` can be used. With this class, the correct event store
is used when loading- or saving an aggregate root.

> **Note:** Events are considered "read-only" and should be saved in persistent storage.

---

## GitHub Actions

### Quick tests
- Composer audit
- PHPUnit (the `unit` test suite)
- PHPStan
- PHP CodeSniffer

---

## Links and references

- [Event sourcing](https://martinfowler.com/eaaDev/EventSourcing.html) explained by _Martin Fowler_
- **Domain-Driven Design** a book by _Eric Evans_
- **Implementing Domain-Driven Design** a book by _Vaughn Vernon_
- The [EventSauce](https://eventsauce.io/) project by _Frank de Jonge_
- The [Broadway](https://github.com/broadway/broadway) project
- [Refactoring.Guru: Design Patterns](https://refactoring.guru/design-patterns)
