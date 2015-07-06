<?php

namespace Ten24\Tests\Formatter;

use Ten24\Component\Ical\Attendee;

class AttendeeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Attendee
     */
    protected $object;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Attendee();
    }

    public function testEmptyConstruct()
    {
        $this->assertNull($this->object->getName());
        $this->assertNull($this->object->getEmail());
    }

    public function testConstruct()
    {
        $a = new Attendee('noone@nowhere.com', 'Jim Nobody');
        $this->assertEquals('noone@nowhere.com', $a->getEmail());
        $this->assertEquals('Jim Nobody', $a->getName());

        $a = new Attendee('noone@nowhere.com');
        $this->assertEquals('noone@nowhere.com', $a->getEmail());
        $this->assertEquals('noone@nowhere.com', $a->getName());
    }

    public function testSetGetEmail()
    {
        $this->object->setEmail('noone@nowhere.com');
        $this->assertEquals('noone@nowhere.com', $this->object->getEmail());
    }

    public function testSetGetName()
    {
        $this->object->setName('Jim Nobody');
        $this->assertEquals('Jim Nobody', $this->object->getName());
    }
}