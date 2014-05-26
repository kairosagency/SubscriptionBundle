<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 21/05/2014
 * Time: 16:45
 */

namespace Kairos\SubscriptionBundle\Twig;

use Kairos\SubscriptionBundle\Form\PaymentType;

interface TwigJsService {

    /**
     * @return string
     */
    public function getScript();
}

