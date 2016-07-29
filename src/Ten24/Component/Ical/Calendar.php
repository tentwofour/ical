<?php

namespace Ten24\Component\Ical;

use Doctrine\Common\Collections\ArrayCollection;

class Calendar extends Base
{
    /**
     * Method PUBLISH
     *
     * @var string
     */
    const METHOD_PUBLISH = 'PUBLISH';

    /**
     * Method REQUEST
     *
     * @var string
     */
    const METHOD_REQUEST = 'REQUEST';

    protected $methods = [
        self::METHOD_PUBLISH,
        self::METHOD_REQUEST
    ];

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $events;

    /**
     * Timezone Id
     *
     * @var string
     */
    protected $timezoneId;

    /**
     * Timezone name
     *
     * @var string
     */
    protected $timezoneName;

    /**
     * Timezone offset start
     *
     * @var string
     */
    protected $timezoneOffsetStart;

    /**
     * Timezone offset end
     *
     * @var string
     */
    protected $timezoneOffsetEnd;

    /**
     * PRODID
     *
     * @var string
     */
    protected $prodId = '-//tentwofour//NONSGML iCal Generator//EN';

    /**
     * @var string
     */
    protected $method;

    /**
     * @param null $timezoneId
     */
    public function __construct($timezoneId = null)
    {
        $tz = (null === $timezoneId)
            ? date_default_timezone_get()
            : $timezoneId;

        $this->setTimezoneId($tz);

        // Create a new \DateTime in the default timezone
        $t        = new \DateTimeZone($this->getTimezoneId());
        $d        = new \DateTime('now', $t);
        $polarity = ($t->getOffset($d) / 3600 === 0) ? '' : ($t->getOffset($d) / 3600 < 0) ? '-' : '+';

        $this->setTimezoneName($d->format('T'));
        $this->setTimezoneOffsetStart($polarity . gmdate('hi', $t->getOffset($d)));
        $this->setTimezoneOffsetEnd($polarity . gmdate('hi', $t->getOffset($d)));
        $this->events = new ArrayCollection();

        return $this;
    }

    /**
     * Generates the ics data
     */
    public function generate()
    {
        $content = "BEGIN:VCALENDAR"
                   . static::ICS_EOL
                   . "PRODID:{$this->getProdId()}"
                   . static::ICS_EOL
                   . "VERSION:2.0"
                   . static::ICS_EOL
                   . "CALSCALE:GREGORIAN"
                   . static::ICS_EOL
                   . "METHOD:{$this->getMethod()}"
                   . static::ICS_EOL
                   // Non-standard field
                   . "X-WR-TIMEZONE:{$this->getTimezoneId()}"
                   . static::ICS_EOL
                   . "BEGIN:VTIMEZONE"
                   . static::ICS_EOL
                   . "TZID:{$this->getTimezoneId()}"
                   . static::ICS_EOL
                   . "BEGIN:STANDARD"
                   . static::ICS_EOL
                   . "DTSTART:20000101T000000"
                   . static::ICS_EOL
                   . "RRULE:FREQ=YEARLY;BYMONTH=1"
                   . static::ICS_EOL
                   . "TZNAME:{$this->getTimezoneName()}"
                   . static::ICS_EOL
                   . "TZOFFSETFROM:{$this->getTimezoneOffsetStart()}"
                   . static::ICS_EOL
                   . "TZOFFSETTO:{$this->getTimezoneOffsetEnd()}"
                   . static::ICS_EOL
                   . "END:STANDARD"
                   . static::ICS_EOL
                   . "END:VTIMEZONE"
                   . static::ICS_EOL
                   . $this->getGeneratedEvents()
                   . "END:VCALENDAR"
                   . static::ICS_EOL;

        return $content;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEvents()
    {
        return $this->events;
    }

    public function getGeneratedEvents()
    {
        $out = '';

        /** @var \Ten24\Component\Ical\Event $event */
        foreach ($this->events as $event)
        {
            $out .= $event->generate();
        }

        return $out;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $events
     *
     * @return $this
     */
    public function setEvents(ArrayCollection $events)
    {
        $this->events = $events;

        return $this;
    }

    /**
     * @param \Ten24\Component\Ical\Event $event
     *
     * @return $this
     */
    public function addEvent(Event $event)
    {
        if (!$this->events->contains($event))
        {
            $this->events->add($event);
        }

        return $this;
    }

    /**
     * @param \Ten24\Component\Ical\Event $event
     *
     * @return $this
     */
    public function removeEvent(Event $event)
    {
        $this->events->removeElement($event);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimezoneId()
    {
        return $this->timezoneId;
    }

    /**
     * Gets the formatted timezone id (ie: America/Los Angeles => America-Los_Angeles)
     */
    public function getFormattedTimezoneId()
    {
        return str_replace(['/', ' '], ['-', '_'], $this->timezoneId);
    }

    /**
     * @param mixed $timezoneId
     *
     * @return Calendar
     */
    public function setTimezoneId($timezoneId)
    {
        $validTimezones = timezone_identifiers_list();

        if (!in_array($timezoneId, $validTimezones))
        {
            throw new \RuntimeException('Invalid Timezone Identifier: ' . $timezoneId);
        }

        $this->timezoneId = $timezoneId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimezoneName()
    {
        return $this->timezoneName;
    }

    /**
     * @param mixed $timezoneName
     *
     * @return Calendar
     */
    public function setTimezoneName($timezoneName)
    {
        $this->timezoneName = $timezoneName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimezoneOffsetStart()
    {
        return $this->timezoneOffsetStart;
    }

    /**
     * @param mixed $timezoneOffsetStart
     *
     * @return Calendar
     */
    public function setTimezoneOffsetStart($timezoneOffsetStart)
    {
        $this->timezoneOffsetStart = $timezoneOffsetStart;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimezoneOffsetEnd()
    {
        return $this->timezoneOffsetEnd;
    }

    /**
     * @param mixed $timezoneOffsetEnd
     *
     * @return Calendar
     */
    public function setTimezoneOffsetEnd($timezoneOffsetEnd)
    {
        $this->timezoneOffsetEnd = $timezoneOffsetEnd;

        return $this;
    }

    /**
     * @return string
     */
    public function getProdId()
    {
        return $this->prodId;
    }

    /**
     * @param string $prodId
     *
     * @return $this
     *
     * @todo - validate the string passed
     */
    public function setProdId($prodId)
    {
        $this->prodId = $prodId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return (null === $this->method) ? self::METHOD_PUBLISH : $this->method;
    }

    /**
     * @param $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = (in_array($method, $this->methods)) ? $method : self::METHOD_PUBLISH;

        return $this;
    }

    /**
     * Saves the current data to the specified file.
     *
     * @param string $filename
     *
     * @return boolean
     */
    public function save($path = null,
                         $filename = null)
    {
        if (is_null(self::$data) || is_null($path) || is_null($filename))
        {
            return false;
        }

        if (substr($path, -1) !== DIRECTORY_SEPARATOR)
        {
            $path .= DIRECTORY_SEPARATOR;
        }

        if (substr($filename, -3) !== self::FILE_EXTENSION)
        {
            $filename .= self::FILE_EXTENSION;
        }

        self::$path     = $path;
        self::$filename = $filename;

        if (@file_put_contents(self::$path . self::$filename, trim(self::$data)))
        {
            $this->setFileSize(strlen(self::$data));

            return true;
        }

        return false;
    }

    public function setFileSize($val)
    {
        self::$file_size = $val;
    }

    public function getFileSize()
    {
        return self::$file_size;
    }

    public function getDownloadHeaders()
    {
        return [
            'Content-type'        => 'text/calendar',
            'Pragma'              => 'public',
            'Content-Disposition' => 'attachment; filename="' . self::$filename . '"'
        ];

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"invite.ics\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . strlen($generate));
    }
}
