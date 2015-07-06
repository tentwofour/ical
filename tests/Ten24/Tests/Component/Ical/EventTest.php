<?php

namespace Ten24\Tests\Formatter;

use Doctrine\Common\Collections\ArrayCollection;
use Ten24\Component\Ical\Attendee;
use Ten24\Component\Ical\Event;

class EventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Event
     */
    protected $object;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Event;
    }

    public function testEmptyConstruct()
    {
        $e = new Event();
        $this->assertCount(0, $e->getAttendees());
        $this->assertEmpty($e->getName());
        $this->assertEmpty($e->getStartDate());
        $this->assertEmpty($e->getEndDate());
        $this->assertNotEmpty($e->getGuid());
    }

    public function testConstruct()
    {
        $attendees = new ArrayCollection([
                                             new Attendee('noone@nowhere.com'),
                                             new Attendee('noone_again@nowhere.com')
                                         ]);
        $startDate = new \DateTime('2014-01-01 09:00:00');
        $endDate   = new \DateTime('2014-01-01 10:00:00');
        $e         = new Event('Sample Event', $startDate, $endDate, $attendees, 'my-guid');

        $this->assertCount(2, $e->getAttendees());
        $this->assertNotEmpty($e->getGuid());
        $this->assertEquals('my-guid', $e->getGuid());
    }

    public function testSetGetAttendees()
    {
        $attendees = new ArrayCollection([
                                             new Attendee('noone@nowhere.com'),
                                             new Attendee('noone_again@nowhere.com')
                                         ]);

        $this->object->setAttendees($attendees);
        $this->assertSame($attendees, $this->object->getAttendees());
    }

    public function testAddRemoveAttendees()
    {
        $a  = new Attendee('noone@nowhere.com');
        $a2 = new Attendee('noone@nowhere.com');

        $this->object->addAttendee($a);
        $this->assertCount(1, $this->object->getAttendees());

        $this->object->addAttendee($a2);
        $this->assertCount(2, $this->object->getAttendees());

        $this->object->removeAttendee($a);
        $this->assertCount(1, $this->object->getAttendees());
        $this->assertFalse($this->object->getAttendees()->contains($a));
    }

    public function testGenerate()
    {
        $e         = $this->object;
        $attendees = new ArrayCollection([
                                             new Attendee('bool.ian@nowhere.com', 'Ian Bool'),
                                             new Attendee('incognito.bill@nowhere.com', 'Bill Incognito')
                                         ]);

        $e->setFromName('Jim Nobody')
          ->setFromEmail('nobody.jim@nowhere.com')
          ->setClass(Event::CLASS_PRIVATE)
          ->setTransparency(Event::TRANSPARENCY_TRANSPARENT)
          ->setDescription('...')
          ->setCreated(new \DateTime())
          ->setStartDate(new \DateTime('2014-01-01 09:00:00'))
          ->setEndDate(new \DateTime('2014-01-01 10:00:00'))
          ->setName('Top Secret Event.')
          ->setAttendees($attendees)
          ->setLocation('The middle of nowhere');

        $expected = "BEGIN:VEVENT"
                    . Event::ICS_EOL
                    . "UID:{$e->getGuid()}"
                    . Event::ICS_EOL
                    . "DTSTART:{$e->getFormattedStartDate()}"
                    . Event::ICS_EOL
                    . "DTEND:{$e->getFormattedEndDate()}"
                    . Event::ICS_EOL
                    . "DTSTAMP:{$e->getFormattedCreated()}"
                    . Event::ICS_EOL
                    . "ORGANIZER;CN=Jim Nobody:mailto:nobody.jim@nowhere.com"
                    . Event::ICS_EOL
                    . "ATTENDEE;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN=Ian Bool;X-NUM-GUESTS=0:mailto:bool.ian@nowhere.com"
                    . Event::ICS_EOL
                    . "ATTENDEE;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN=Bill Incognito;X-NUM-GUESTS=0:mailto:incognito.bill@nowhere.com"
                    . Event::ICS_EOL
                    . "CREATED:{$e->getFormattedCreated()}"
                    . Event::ICS_EOL
                    . "SUMMARY:Top Secret Event."
                    . Event::ICS_EOL
                    . "DESCRIPTION:..."
                    . Event::ICS_EOL
                    . "LOCATION:The middle of nowhere"
                    . Event::ICS_EOL
                    . "STATUS:NEEDS-ACTION"
                    . Event::ICS_EOL
                    . "LAST-MODIFIED:{$e->getFormattedStartDate()}"
                    . Event::ICS_EOL
                    . "CLASS:PRIVATE"
                    . Event::ICS_EOL
                    . "SEQUENCE:0"
                    . Event::ICS_EOL
                    . "TRANSP:TRANSPARENT"
                    . Event::ICS_EOL
                    . "END:VEVENT"
                    . Event::ICS_EOL;

        $this->assertEquals($expected, $e->generate());

    }

    public function testGenerateWithMissingRequireds()
    {
        $e         = $this->object;
        $attendees = new ArrayCollection([
                                             new Attendee('bool.ian@nowhere.com', 'Ian Bool'),
                                             new Attendee('incognito.bill@nowhere.com', 'Bill Incognito')
                                         ]);

        $e->setFromEmail('nobody.jim@nowhere.com')
          ->setClass(Event::CLASS_PRIVATE)
          ->setTransparency(Event::TRANSPARENCY_TRANSPARENT)
          ->setDescription('...')
          ->setCreated(new \DateTime())
          ->setName('Top Secret Event.')
          ->setAttendees($attendees)
          ->setLocation('The middle of nowhere');

        // Should throw an exception
        $this->setExpectedException('RuntimeException');
        $e->generate();
    }

    public function testFromArray()
    {
        $e = [
            'startDate' => new \DateTime('2014-01-01 09:00:00'),
            'endDate' => new \DateTime('2014-01-01 10:00:00'),
            'name' => 'Top Secret Event.'
        ];

        $this->object->fromArray($e);

        $this->assertEquals('2014-01-01 09:00:00', $this->object->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('2014-01-01 10:00:00', $this->object->getEndDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('Top Secret Event.', $this->object->getName());
    }

    public function testSetGetName()
    {
        $this->object->setName('Test');
        $this->assertEquals('Test', $this->object->getName());
    }

    public function testSetGetDescription()
    {
        $this->object->setdescription('Test');
        $this->assertEquals('Test', $this->object->getDescription());
    }

    public function testSetGetStartDate()
    {
        $d = new \DateTime();
        $this->object->setStartDate($d);
        $this->assertEquals($d, $this->object->getStartDate());
    }

    public function testSetGetEndDate()
    {
        $d = new \DateTime();
        $this->object->setEndDate($d);
        $this->assertEquals($d, $this->object->getEndDate());
    }

    public function testSetGetCreated()
    {
        $d = new \DateTime();
        $this->object->setCreated($d);
        $this->assertEquals($d, $this->object->getCreated());
    }

    public function testSetGetFromEmail()
    {
        $this->object->setFromEmail('noone@nowhere.com');
        $this->assertEquals('noone@nowhere.com', $this->object->getFromEmail());
    }

    public function testSetGetFromName()
    {
        $this->object->setFromName('Tester');
        $this->assertEquals('Tester', $this->object->getFromName());
    }

    public function testSetGetStatus()
    {
        // Default
        $this->assertEquals(Event::STATUS_NEEDS_ACTION, $this->object->getStatus());

        // Set to valid status
        $this->object->setStatus(Event::STATUS_NEEDS_ACTION);
        $this->assertEquals(Event::STATUS_NEEDS_ACTION, $this->object->getStatus());

        // Set to invalid status
        $this->object->setStatus('FAKE');
        $this->assertEquals(Event::STATUS_NEEDS_ACTION, $this->object->getStatus());
    }

    public function testSetGetClass()
    {
        // Default
        $this->assertEquals(Event::CLASS_PUBLIC, $this->object->getClass());

        // Set to valid class
        $this->object->setClass(Event::CLASS_PRIVATE);
        $this->assertEquals(Event::CLASS_PRIVATE, $this->object->getClass());

        // Set to invalid class
        $this->object->setClass('FAKE');
        $this->assertEquals(Event::CLASS_PUBLIC, $this->object->getClass());
    }

    public function testSetGetTransparency()
    {
        // Default
        $this->assertEquals(Event::TRANSPARENCY_OPAQUE, $this->object->getTransparency());

        // Set to valid transparency
        $this->object->setTransparency(Event::TRANSPARENCY_TRANSPARENT);
        $this->assertEquals(Event::TRANSPARENCY_TRANSPARENT, $this->object->getTransparency());

        // Set to invalid transparency
        $this->object->setTransparency('FAKE');
        $this->assertEquals(Event::TRANSPARENCY_OPAQUE, $this->object->getTransparency());
    }

    public function testSetGetLocation()
    {
        $this->object->setLocation('Nowhere');
        $this->assertEquals('Nowhere', $this->object->getLocation());
    }
}