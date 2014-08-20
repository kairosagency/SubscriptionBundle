<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 19/08/2014
 * Time: 18:11
 */

namespace Kairos\SubscriptionBundle\Tests\TestEntities;


class CustomerEntity extends \Kairos\SubscriptionBundle\Model\Customer
{
    public function __construct()
    {
        $this->firstName =  "Ali";
        $this->lastName =   "Baba";
        $this->email =      "ali.baba@gmail.com";
    }
} 