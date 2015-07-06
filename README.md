Easy iCal Generation
===================

```php
use Doctrine\Common\Collections\ArrayCollection;
use Ten24\Component\Ical\Attendee
use Ten24\Component\Ical\Calendar
use Ten24\Component\Ical\Event

// Rely on date_default_timezone_get()
$c = new Calendar();

// Or pass your own
$c = new Calendar('America/Regina');

// Create a basic event via constructor method
$e1 = new Event(
  'Event Name',
  new \DateTime('2014-01-01 09:00:00'),
  new \DateTime('2014-01-01 10:00:00'),
  new ArrayCollection([
    new Attendee('Ian Bool', 'bool.ian@nowhere.com'),
    new Attendee('Bill Incognito', 'bill.incognito@nowhere.com')
  ]),
  'my-guid');

// Add the event to the calendar
$c->addEvent($e1);

// Or, more complete
$e2 = new Event(
  'Top Secret Event',
  new \DateTime('2014-01-01 09:00:00'),
  new \DateTime('2014-01-01 10:00:00'),
  new ArrayCollection([
    new Attendee('Ian Bool', 'bool.ian@nowhere.com'),
    new Attendee('Bill Incognito', 'bill.incognito@nowhere.com')
  ]),
  'my-guid')
  ->setLocation('Top Secret Location')
  ->setDescription('Trust noone.')
  ->setStatus(Event::STATUS_IN_PROCESS)
  ->setClass(Event::CLASS_PRIVATE)
  //...
  ->setTransparency(Event::TRANSPARENCY_TRANSPARENT);

// Add the event to the calendar
$c->addEvent($e2);

// Or by array, useful when pulling from a db, and hydrating as an array
$events = [[
  'name' => 'Top Secret Event',
  'startDate' => new \DateTime('2014-01-01 09:00:00')
  'endDate' => new \DateTime('2014-01-01 09:00:00'),
  'location' => 'Top Secret Location'
  //...
  ],
  [
  'name' => 'Top Secret Event 2',
  'startDate' => new \DateTime('2014-01-01 09:00:00')
  'endDate' => new \DateTime('2014-01-01 09:00:00'),
  'location' => 'Top Secret Location'
  //...
  ]];

foreach($events as $event)
{
  $e = new Event();
  $c->addEvent($e->fromArray($event));
}

// Get the generated calendar
$icalData = $c->generate();
```

Running Tests
=============

```bash
composer install
phpunit -c phpunit.xml.dist
```
