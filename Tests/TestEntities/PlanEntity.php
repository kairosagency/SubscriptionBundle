<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 19/08/2014
 * Time: 18:11
 */

namespace Kairos\SubscriptionBundle\Tests\TestEntities;


class PlanEntity extends \Kairos\SubscriptionBundle\Model\Plan
{
    public function __construct()
    {
        $this->subscriptionPlanId = 'starter_vat';
    }
} 