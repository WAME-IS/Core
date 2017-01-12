<?php

namespace Wame\Core\Filters;

use Nette\Object;
use Wame\Utils\Date;

class TimeAgoFilter extends Object
{
    /**
     * @param string $date
     * @return string
     */
    public function __invoke($date)
    {
        return Date::timeAgoInWords($date);
    }
    
}
