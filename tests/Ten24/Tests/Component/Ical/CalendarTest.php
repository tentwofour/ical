<?php

namespace Ten24\Tests\Formatter;

use Doctrine\Common\Collections\ArrayCollection;
use Ten24\Component\Ical\Calendar;
use Ten24\Component\Ical\Event;

class CalendarTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyConstruct()
    {
        $calendar = new Calendar();
        $tz       = new \DateTimeZone($calendar->getTimezoneId());
        $dt       = new \DateTime('now', $tz);

        $this->assertEquals(date_default_timezone_get(), $calendar->getTimezoneId());
        $this->assertEquals($dt->format('T'), $calendar->getTimezoneName());
        $this->assertEquals($tz->getOffset(new \DateTime()), $calendar->getTimezoneOffsetStart());
        $this->assertEquals($tz->getOffset(new \DateTime()), $calendar->getTimezoneOffsetEnd());
    }

    public function testConstruct()
    {
        $calendar = new Calendar('America/Regina');
        $tz       = new \DateTimeZone($calendar->getTimezoneId());
        $dt       = new \DateTime('now', $tz);

        $this->assertEquals(date_default_timezone_get(), $calendar->getTimezoneId());
        $this->assertEquals($dt->format('T'), $calendar->getTimezoneName());
        $this->assertEquals($tz->getOffset(new \DateTime()), $calendar->getTimezoneOffsetStart());
        $this->assertEquals($tz->getOffset(new \DateTime()), $calendar->getTimezoneOffsetEnd());
    }

    public function testGenerateWithEmptyConstructor()
    {
        $c  = new Calendar();
        $tz = new \DateTimeZone($c->getTimezoneId());
        $dt = new \DateTime('now', $tz);

        $expected = "BEGIN:VCALENDAR"
                    . Calendar::ICS_EOL
                    . "PRODID:{$c->getProdId()}"
                    . Calendar::ICS_EOL
                    . "VERSION:2.0"
                    . Calendar::ICS_EOL
                    . "CALSCALE:GREGORIAN"
                    . Calendar::ICS_EOL
                    . "METHOD:{$c->getMethod()}"
                    . Calendar::ICS_EOL
                    . "X-WR-TIMEZONE:{$c->getFormattedTimezoneId()}"
                    . Calendar::ICS_EOL
                    . "BEGIN:VTIMEZONE"
                    . Calendar::ICS_EOL
                    . "TZID:{$c->getFormattedTimezoneId()}"
                    . Calendar::ICS_EOL
                    . "BEGIN:STANDARD"
                    . Calendar::ICS_EOL
                    . "DTSTART:20000101T000000"
                    . Calendar::ICS_EOL
                    . "RRULE:FREQ=YEARLY;BYMONTH=1"
                    . Calendar::ICS_EOL
                    . "TZNAME:{$c->getTimezoneName()}"
                    . Calendar::ICS_EOL
                    . "TZOFFSETFROM:{$c->getTimezoneOffsetStart()}"
                    . Calendar::ICS_EOL
                    . "TZOFFSETTO:{$c->getTimezoneOffsetEnd()}"
                    . Calendar::ICS_EOL
                    . "END:STANDARD"
                    . Calendar::ICS_EOL
                    . "END:VTIMEZONE"
                    . Calendar::ICS_EOL
                    . "END:VCALENDAR"
                    . Calendar::ICS_EOL;

        $this->assertEquals($expected, $c->generate());
    }

    public function testGenerate()
    {
        $c        = new Calendar('America/Argentina/San_Luis');
        $expected = "BEGIN:VCALENDAR"
                    . Calendar::ICS_EOL
                    . "PRODID:{$c->getProdId()}"
                    . Calendar::ICS_EOL
                    . "VERSION:2.0"
                    . Calendar::ICS_EOL
                    . "CALSCALE:GREGORIAN"
                    . Calendar::ICS_EOL
                    . "METHOD:{$c->getMethod()}"
                    . Calendar::ICS_EOL
                    . "X-WR-TIMEZONE:{$c->getFormattedTimezoneId()}"
                    . Calendar::ICS_EOL
                    . "BEGIN:VTIMEZONE"
                    . Calendar::ICS_EOL
                    . "TZID:{$c->getFormattedTimezoneId()}"
                    . Calendar::ICS_EOL
                    . "BEGIN:STANDARD"
                    . Calendar::ICS_EOL
                    . "DTSTART:20000101T000000"
                    . Calendar::ICS_EOL
                    . "RRULE:FREQ=YEARLY;BYMONTH=1"
                    . Calendar::ICS_EOL
                    . "TZNAME:{$c->getTimezoneName()}"
                    . Calendar::ICS_EOL
                    . "TZOFFSETFROM:{$c->getTimezoneOffsetStart()}"
                    . Calendar::ICS_EOL
                    . "TZOFFSETTO:{$c->getTimezoneOffsetEnd()}"
                    . Calendar::ICS_EOL
                    . "END:STANDARD"
                    . Calendar::ICS_EOL
                    . "END:VTIMEZONE"
                    . Calendar::ICS_EOL
                    . "END:VCALENDAR"
                    . Calendar::ICS_EOL;

        $this->assertEquals($expected, $c->generate());
    }

    public function testSetGetTimezoneOffsetStart()
    {
        $c = new Calendar();
        $c->setTimezoneOffsetStart('-600');
        $this->assertEquals('-600', $c->getTimezoneOffsetStart());
    }

    public function testSetGetTimezoneOffsetEnd()
    {
        $c = new Calendar();
        $c->setTimezoneOffsetEnd('-600');
        $this->assertEquals('-600', $c->getTimezoneOffsetEnd());
    }

    public function testSetGetTimezoneId()
    {
        $c = new Calendar();
        $c->setTimezoneId('America/Regina');
        $this->assertEquals('America/Regina', $c->getTimezoneId());
    }

    public function testSetGetTimezoneName()
    {
        $c = new Calendar();
        $c->setTimezoneName('CST');
        $this->assertEquals('CST', $c->getTimezoneName());
    }

    public function testSetGetEvents()
    {
        $c      = new Calendar();
        $events = new ArrayCollection([
                                          new Event('1'),
                                          new Event('2'),
                                          new Event('3'),
                                          new Event('4'),
                                      ]);

        $c->setEvents($events);

        $this->assertSame($events, $c->getEvents());
    }

    public function testAddRemoveEvents()
    {
        $c  = new Calendar();
        $e  = new Event('1');
        $e2 = new Event('2');

        $c->addEvent($e);
        $this->assertCount(1, $c->getEvents());

        $c->addEvent($e2);
        $this->assertCount(2, $c->getEvents());

        $c->removeEvent($e);
        $this->assertCount(1, $c->getEvents());
        $this->assertFalse($c->getEvents()->contains($e));
    }

    public function testSetGetProdId()
    {
        $c = new Calendar();
        $c->setProdid('MyProdId');
        $this->assertEquals('MyProdId', $c->getProdid());
    }

    public function testSetGetMethod()
    {
        $c = new Calendar();
        $c->setMethod(Calendar::METHOD_PUBLISH);
        $this->assertEquals(Calendar::METHOD_PUBLISH, $c->getMethod());

        $c->setMethod('FAKE');
        $this->assertEquals(Calendar::METHOD_PUBLISH, $c->getMethod());
    }
}