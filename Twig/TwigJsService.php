<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 21/05/2014
 * Time: 16:45
 */

namespace Kairos\SubscriptionBundle\Twig;


interface TwigJsService {

    /**
     * @return string
     */
    public function getScript();

    /**
     * @return string
     */
    public function getScriptV2();
}

