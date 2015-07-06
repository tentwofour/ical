<?php

namespace Ten24\Component\Ical;

use Doctrine\Common\Collections\ArrayCollection;

class Event extends Base
{
    /**
     * Event Status CANCELLED
     *
     * @var string
     */
    const STATUS_CANCELLED = 'CANCELLED';

    /**
     * Event Status COMPLETED
     *
     * @var string
     */
    const STATUS_COMPLETED = 'COMPLETED';

    /**
     * Event Status IN-PROCESS
     *
     * @var string
     */
    const STATUS_IN_PROCESS = 'IN-PROCESS';

    /**
     * Event Status NEEDS-ACTION
     *
     * @var string
     */
    const STATUS_NEEDS_ACTION = 'NEEDS-ACTION';

    /**
     * Event Class CONFIDENTIAL
     *
     * @var string
     */
    const CLASS_CONFIDENTIAL = 'CONFIDENTIAL';

    /**
     * Event Class PRIVATE
     *
     * @var string
     */
    const CLASS_PRIVATE = 'PRIVATE';

    /**
     * Event Class PUBLIC
     *
     * @var string
     */
    const CLASS_PUBLIC = 'PUBLIC';

    /**
     * Event transparency opaque (visible in busy-time searches)
     *
     * @var string
     */
    const TRANSPARENCY_OPAQUE = 'OPAQUE';

    /**
     * Event transparency transparent (invisible in busy-time searches)
     *
     * @var string
     */
    const TRANSPARENCY_TRANSPARENT = 'TRANSPARENT';

    /**
     * @var array
     */
    private $statuses = [
        self::STATUS_CANCELLED,
        self::STATUS_COMPLETED,
        self::STATUS_IN_PROCESS,
        self::STATUS_NEEDS_ACTION,
    ];

    /**
     * @var array
     */
    private $classes = [
        self::CLASS_CONFIDENTIAL,
        self::CLASS_PRIVATE,
        self::CLASS_PUBLIC,
    ];

    /**
     * @var array
     */
    private $transparencies = [
        self::TRANSPARENCY_OPAQUE,
        self::TRANSPARENCY_TRANSPARENT
    ];

    /**
     *
     * The name of the event
     *
     * @var string
     */
    private $name;

    /**
     * The invite description content
     *
     * @var string
     */
    private $description;

    /**
     * The event startDate date
     *
     * @var \DateTime
     */
    private $startDate;

    /**
     * The event endDate date
     *
     * @var \DateTime
     */
    private $endDate;

    /**
     * The event creation date
     *
     * @var \DateTime
     */
    private $created;

    /**
     * The name of the user the invite is coming from
     *
     * @var string
     */
    private $fromName;

    /**
     * Sender's email
     *
     * @var string
     */
    private $fromEmail;

    /**
     * The status of the event (IN-PROGRESS, NEEDS_ACTION...)
     *
     * @var string
     */
    private $status;

    /**
     * The event class (PRIVATE, PUBLIC, CONFIDENTIAL)
     *
     * @var string
     */
    private $class;

    /**
     * The transparency of the event (OPAQUE, TRANSPARENT)
     *
     * @var string
     */
    private $transparency;

    /**
     * The event attendees
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $attendees;

    /**
     * The event location
     *
     * @var string
     */
    private $location;

    /**
     * The GUID of the event
     *
     * @var string
     */
    private $guid;


    /**
     * @param null                                         $name
     * @param null                                         $startDate
     * @param null                                         $endDate
     * @param \Doctrine\Common\Collections\ArrayCollection $attendees
     * @param null                                         $guid
     */
    public function __construct($name = null,
                                $startDate = null,
                                $endDate = null,
                                ArrayCollection $attendees = null,
                                $guid = null)

    {
        $this->setName($name);

        if ($startDate instanceof \DateTime)
        {
            $this->setStartDate($startDate);
        }

        if ($endDate instanceof \DateTime)
        {
            $this->setEndDate($startDate);
        }

        $this->setAttendees((null !== $attendees && $attendees instanceof ArrayCollection)
                                ? $attendees
                                : new ArrayCollection());

        $this->setGuid($guid);

        return $this;
    }

    public function generate()
    {
        if (!$this->isValid())
        {
            throw new \RuntimeException('One or more required fields are not set.');
        }

        $content = "BEGIN:VEVENT"
                   . static::ICS_EOL
                   . "UID:{$this->getGuid()}"
                   . static::ICS_EOL
                   . "DTSTART:{$this->getFormattedStartDate()}"
                   . static::ICS_EOL
                   . "DTEND:{$this->getFormattedEndDate()}"
                   . static::ICS_EOL
                   . "DTSTAMP:{$this->getFormattedCreated()}"
                   . static::ICS_EOL
                   . "ORGANIZER;CN={$this->getFromName()}:mailto:{$this->getFromEmail()}"
                   . static::ICS_EOL;

        /** @var \Ten24\Component\Ical\Attendee $attendee */
        foreach ($this->getAttendees() as $attendee)
        {
            $content .=
                "ATTENDEE;PARTSTAT={$this->getStatus()};RSVP=TRUE;CN={$attendee->getName()};X-NUM-GUESTS=0:mailto:{$attendee->getEmail()}"
                .
                static::ICS_EOL;
        }

        $content .= "CREATED:{$this->getFormattedCreated()}"
                    . static::ICS_EOL
                    . "SUMMARY:{$this->getName()}"
                    . static::ICS_EOL
                    . "DESCRIPTION:{$this->getDescription()}"
                    . static::ICS_EOL
                    . "LOCATION:{$this->getLocation()}"
                    . static::ICS_EOL
                    . "STATUS:{$this->getStatus()}"
                    . static::ICS_EOL
                    . "LAST-MODIFIED:{$this->getFormattedStartDate()}"
                    . static::ICS_EOL
                    . "CLASS:{$this->getClass()}"
                    . static::ICS_EOL
                    . "SEQUENCE:0"
                    . static::ICS_EOL
                    . "TRANSP:{$this->getTransparency()}"
                    . static::ICS_EOL
                    . "END:VEVENT"
                    . static::ICS_EOL;

        return $content;
    }

    /**
     * @return string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * @param string $guid
     *
     * @return $this
     */
    public function setGuid($guid = null)
    {
        if (null === $guid)
        {
            $this->guid = uniqid(mt_rand());
        }
        else
        {
            $this->guid = $guid;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     *
     * @return $this
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return null === $this->created ? $this->startDate : $this->created;
    }

    /**
     * @return string
     */
    public function getFormattedCreated()
    {
        return $this->created->format(static::DATEFORMAT);
    }

    /**
     * @param \DateTime $created
     *
     * @return $this
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Get the formatted start date for the event generation
     *
     * @return string
     */
    public function getFormattedStartDate()
    {
        return $this->startDate->format(self::DATEFORMAT);
    }

    /**
     * @param \DateTime $startDate
     *
     * @return $this
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Get the formatted end date for the event generation
     *
     * @return string
     */
    public function getFormattedEndDate()
    {
        return $this->endDate->format(self::DATEFORMAT);
    }

    /**
     * @param \DateTime $endDate
     *
     * @return $this
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @param string $fromName
     *
     * @return $this
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @param string $fromEmail
     *
     * @return $this
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     * Set attendees
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $attendees
     *
     * @return $this
     */
    public function setAttendees(ArrayCollection $attendees)
    {
        $this->attendees = $attendees;

        return $this;
    }

    /**
     * Add an attendee
     *
     * @return $this
     */
    public function addAttendee(Attendee $attendee)
    {
        if (!$this->attendees->contains($attendee))
        {
            $this->attendees->add($attendee);
        }

        return $this;
    }

    /**
     * Remove an attendee
     *
     * @return $this
     */
    public function removeAttendee(Attendee $attendee)
    {
        $this->attendees->removeElement($attendee);

        return $this;
    }

    /**
     * Clear all attendees
     *
     * @return $this
     */
    public function clearAttendees()
    {
        $this->attendees->clear();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return null === $this->status ? self::STATUS_NEEDS_ACTION : $this->status;
    }

    /**
     * @param $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = (!in_array($status, $this->statuses)) ? self::STATUS_NEEDS_ACTION : $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return null === $this->class ? self::CLASS_PUBLIC : $this->class;
    }

    /**
     * Set Event Class
     * Defaults to PUBLIC if passed argument does not match an iCal Status
     *
     * @param $class
     *
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = (!in_array($class, $this->classes)) ? self::CLASS_PUBLIC : $class;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTransparency()
    {
        return null === $this->transparency ? self::TRANSPARENCY_OPAQUE : $this->transparency;
    }

    /**
     * Set Event Transparency
     * Defaults to OPAQUE if passed argument does not match an iCal Status
     *
     * @param $transparency
     *
     * @return $this
     */
    public function setTransparency($transparency)
    {
        $this->transparency =
            (!in_array($transparency, $this->transparencies)) ? self::TRANSPARENCY_OPAQUE : $transparency;

        return $this;
    }

    /**
     * Validate the event
     * Fields that are required:
     * - Name
     * - StartDate
     * - EndDate
     *
     * @return boolean
     */
    protected function isValid()
    {
        return (null !== $this->name) &&
               (null !== $this->startDate) &&
               (null !== $this->endDate);
    }

    /**
     * Create an event from an array
     *
     * @param array $event
     */
    public function fromArray(array $event = [])
    {
        $c = new \ReflectionClass('\Ten24\Component\Ical\Event');

        foreach($c->getProperties() as $property)
        {
            if (isset($event[$property->getName()]))
            {
                $method = 'set'.ucfirst($property->getName());
                $this->$method($event[$property->getName()]);
            }
        }

    }
}