<?php

declare(strict_types=1);

namespace XackiGiFF\MPEPerms\utils;

class Utils {

    /**
     * @param $date
     * @return int
     * Example: $date = '1d2h3m';
     */
    public function date2Int($date)
    {
        if(preg_match("/([0-9]+)d([0-9]+)h([0-9]+)m/", $date, $result_array) and count($result_array) === 4)
            return time() + ($result_array[1] * 86400) + ($result_array[2] * 3600) + ($result_array[3] * 60);
        return -1;
    }
}