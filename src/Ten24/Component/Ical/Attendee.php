<?php

namespace Ten24\Component\Ical;

class Attendee extends Base
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    public function __construct($email = null, $name = null)
    {
        $this->email = $email;
        $this->name = (null === $name) ? $email : $name;
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
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
}