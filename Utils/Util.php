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
    public static function braintreeErrorsToArray($d) {
        if($d instanceof \Braintree_Error_Validation) {
            $d = array('attribute' => $d->attribute, 'code' => $d->code, 'message' => $d->message);
        }
        return is_array($d) ? array_map(__METHOD__, $d) : $d;
    }

}