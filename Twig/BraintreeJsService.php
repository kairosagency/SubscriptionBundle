<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 21/05/2014
 * Time: 16:45
 */

namespace Kairos\SubscriptionBundle\Twig;

use Kairos\SubscriptionBundle\Adapter\BraintreeSubscriptionAdapter;
use Kairos\SubscriptionBundle\Form\PaymentType;
use Kairos\SubscriptionBundle\Model\CustomerInterface;

class BraintreeJsService extends \Twig_Extension implements TwigJsService{

    /**
     * @var string client side encryption key
     */
    private $key;

    /**
     * @var \Kairos\SubscriptionBundle\Form\PaymentType
     */
    private $formType;

    /**
     * @var \Kairos\SubscriptionBundle\Adapter\BraintreeSubscriptionAdapter
     */
    private $subscriptionAdapter;

    /**
     * @param PaymentType $formType
     * @param $trackingID
     * @param $domain
     */
    public function __construct(PaymentType $formType, BraintreeSubscriptionAdapter $subscriptionAdapter, $key)
    {
        $this->key          = $key;
        $this->formType     = $formType;
        $this->subscriptionAdapter     = $subscriptionAdapter;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kairos_subscription_js_braintree';
    }


    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSubscriptionJsScript', array($this, 'getSubscriptionJsScript'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('getBraintreeJsV1', array($this, 'getSubscriptionJsScript'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('getBraintreeJsV2', array($this, 'getBraintreeJsV2'), array('is_safe' => array('html'))),
        );
    }


    /**
     * @return string
     */
    public function getSubscriptionJsScript($formName = null) {

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

    /**
     * @return string
     */
    public function getBraintreeJsV2(CustomerInterface $customer, $formName = null) {

        if(is_null($formName))
            $_formName = $this->formType->getName();
        else
            $_formName = $formName;

        $script = "<script src=\"https://js.braintreegateway.com/v2/braintree.js\"></script>"
            . "<script>braintree.setup(\"".$this->subscriptionAdapter->generateClientToken($customer)."\", \"custom\", {id: \"".$formName."\"});"
            . "</script>";

        return $script;
    }
}

