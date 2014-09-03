<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 19/08/2014
 * Time: 18:11
 */

namespace Kairos\SubscriptionBundle\Tests\TestEntities;


use Doctrine\Common\Collections\ArrayCollection;

class CustomerEntity extends \Kairos\SubscriptionBundle\Model\Customer
{
    public function __construct()
    {
        $this->creditCards = new ArrayCollection();
        $this->firstName =  "Ali".rand(0,1000);
        $this->lastName =   "Baba".rand(0,1000);
        $this->email =      "ali.baba".rand(0,1000)."@gmail.com";
    }
} 