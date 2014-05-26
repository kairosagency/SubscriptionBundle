<?php
namespace Kairos\SubscriptionBundle\Controller;

use Kairos\AccountBundle\Entity\CreditCard;
use Kairos\AccountBundle\Entity\Subscription;
use Kairos\SubscriptionBundle\Event\SubscriptionEvent;
use Kairos\SubscriptionBundle\KairosSubscriptionEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Braintree_WebhookTesting, Braintree_WebhookNotification;

/**
 * Webhook controller.
 */
class paymentController extends Controller
{

    /**
     *
     */
    public function paymentAction(Request $request, $customerId)
    {

        $customer = $this->container->get('kairos_user.manager.owner')->findAndCheck($customerId);

        $creditCard = new CreditCard();

        $form = $this->createForm(
            'kairos_subscription_payment_form',
            $creditCard
        );

        $form->handleRequest($request);

        if($form->isValid()) {
            $subscriptionAdapter = $this->container->get('kairos_subscription.subscription_adapter');
            $customer->addCreditCard($creditCard);
            $subscriptionAdapter->createCreditCard($creditCard);

            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->persist($creditCard);
            $em->flush();

            return new Response('payment token : ' . $creditCard->getToken());
        }

        return $this->container->get('templating')->renderResponse(
            'KairosSubscriptionBundle:Payment:payment.html.twig',
            array(
                'form'              => $form->createView(),
                'customer'              => $customer
            )
        );
    }

    /**
     *
     */
    public function subscriptionAction(Request $request, $customerId, $planId)
    {

        $customer = $this->container->get('kairos_subscription.manager.owner')->findAndCheck($customerId);

        $plan = $this->container->get('kairos_subscription.manager.plan')->find($planId);

        $subscriptionAdapter = $this->container->get('kairos_subscription.subscription_adapter');

        if(!$customer->getSubscription())
        {
            $subscription = new Subscription();
            $subscription->setPlan($plan);
            $subscription->setCustomer($customer);
        }

        $subscriptionAdapter->createSubscription($customer->getSubscription());

        if($customer->getSubscription()->hasErrors()) {
            var_dump($customer->getSubscription()->getErrors());
            exit();
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($customer->getSubscription());
        $em->persist($customer->getSubscription()->getTransactions()->last());
        $em->flush();

        $dispatcher = $this->get('event_dispatcher');

        $event =  new SubscriptionEvent($customer->getSubscription());
        $dispatcher->dispatch(KairosSubscriptionEvents::SUBSCRIPTION_CHARGED_SUCCESSFULLY, $event);

        return new Response('subscription token : ' . $customer->getSubscription()->getSubscriptionId());

    }
}