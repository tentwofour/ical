<?php

namespace Ten24\Component\Ical;

class Base
{
    /**
     * EOL for ics output
     *
     * @var string
     */
    const ICS_EOL = "\r\n";

    /**
     * Date format
     *
     * @var string
     */
    const DATEFORMAT = 'Ymd\THis';

    /**
     * Date format UTC
     *
     * @var string
     */
    const DATEFORMAT_UTC = 'Ymd\THis\Z';
}