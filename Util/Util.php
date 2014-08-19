<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 19/08/2014
 * Time: 11:52
 */

namespace Kairos\SubscriptionBundle\Utils;


class Util {
    /**
     * @param $d
     * @return array
     */
    public static function objectToArray($d) {
        if (is_object($d))
            $d = get_object_vars($d);

        return is_array($d) ? array_map(__METHOD__, $d) : $d;
    }

    /**
     * @param $d
     * @return object
     */
    public static function arrayToObject($d) {
        return is_array($d) ? (object) array_map(__METHOD__, $d) : $d;
    }
} 