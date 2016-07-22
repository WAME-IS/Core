<?php

namespace Wame\Core\Filters;

use Nette\Object;

class TimeAgoFilter extends Object
{
    /**
     * @param string $date
     * @return string
     */
    public function __invoke($date)
    {
        return \Wame\Utils\Date::timeAgoInWords($date);
    }
    
}
