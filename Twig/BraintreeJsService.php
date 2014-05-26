<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 21/05/2014
 * Time: 16:45
 */

namespace Kairos\SubscriptionBundle\Twig;

use Kairos\SubscriptionBundle\Form\PaymentType;

class BraintreeJsService implements TwigJsService {

    private $key;

    private $formType;

    /**
     * @param PaymentType $formType
     * @param $trackingID
     * @param $domain
     */
    public function __construct(PaymentType $formType, $key)
    {
        $this->key          = $key;
        $this->formType     = $formType;
    }


    /**
     * @return string
     */
    public function getScript($formName = null) {

        if(is_null($formName))
            $_formName = $this->formType->getName();
        else
            $_formName = $formName;

        $script = "<script src=\"https://js.braintreegateway.com/v1/braintree.js\"></script>"
            . "<script>var braintree = Braintree.create(\"". $this->key ."\");"
            . "braintree.onSubmitEncryptForm('" . $_formName . "');"
            . "</script>";

        return $script;
    }
}

