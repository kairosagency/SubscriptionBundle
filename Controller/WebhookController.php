<?php
namespace Kairos\SubscriptionBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Kairos\SubscriptionBundle\Event\SubscriptionEvent;
use Kairos\SubscriptionBundle\Model\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Kairos\SubscriptionBundle\KairosSubscriptionEvents;
use Braintree_WebhookNotification;
use Symfony\Component\HttpFoundation\Response;

/**
 * Webhook controller.
 */
class WebhookController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function subscriptionWebhookAction(Request $request)
    {
        $subscriptionAdapter = $this->get('kairos_subscription.subscription_adapter');

        if($request->isMethod('POST')) {

            $webhookNotification = $subscriptionAdapter->parseWebhook($request);

            $subscriptions = $this->get('kairos_subscription.subscription.manager')->findBySubscriptionId($webhookNotification->subscription->id);

            // wait a little bit of time and
            // try 3 times to find the subscription given the id
            $tries = 0;
            while(count($subscriptions) == 0 && $tries < 2) {
                sleep(3);
                $subscriptions = $this->get('kairos_subscription.subscription.manager')->findBySubscriptionId($webhookNotification->subscription->id);
                $tries++;
            }

            // todo : keep that for testing but then delete it
            // debug webhook result
            ob_start();
            echo "subscription id";
            echo chr(13);
            var_dump($webhookNotification->subscription->id);
            echo chr(13).chr(13).chr(13);
            echo "notification type";
            echo chr(13);
            var_dump($webhookNotification->kind);
            echo chr(13).chr(13).chr(13);
            echo "Transaction infos";
            echo chr(13);
            var_dump($webhookNotification->subscription->transactions);
            $result = ob_get_clean();



            if(count($subscriptions) > 0) {

                $subscription = $subscriptions[0];

                $result .= "Subscription id" . chr(13) . $subscription->getId();
                $result .= chr(13)."fin";
                $this->get('logger')->info($result);
                /*$message = \Swift_Message::newInstance()
                    ->setSubject('braintree webhook')
                    ->setFrom('coucou@kairostag.com')
                    ->setTo('infra@kairostag.com')
                    ->setBody($result);
                $this->get('mailer')->send($message);*/

                $eventName = $subscriptionAdapter->getSubscriptionEvent($subscription, $webhookNotification);

                if($eventName) {
                    $dispatcher = $this->get('event_dispatcher');
                    $this->get('kairos_subscription.subscription.manager')->save($subscription);
                    $event =  new SubscriptionEvent($subscription, $webhookNotification);
                    $dispatcher->dispatch($eventName, $event);
                    return new Response('ok', 200);
                }
            }
            else {
                $result .= chr(13)."fin";

                $this->get('logger')->info($result);
                /*$message = \Swift_Message::newInstance()
                    ->setSubject('braintree webhook')
                    ->setFrom('coucou@kairostag.com')
                    ->setTo('infra@kairostag.com')
                    ->setBody($result);
                $this->get('mailer')->send($message);*/

                $this->get('logger')->error('[subscription bundle webhook] Could not find any subscription with id ' . $webhookNotification->subscription->id);
                return new Response('nok', 200);
            }

            return new Response('nok', 400);
        }
        else if($request->isMethod('GET')) {
            /*$message = \Swift_Message::newInstance()
                ->setSubject('braintree webhook')
                ->setFrom('coucou@kairostag.com')
                ->setTo('infra@kairostag.com')
                ->setBody('webhookcheck')
            ;
            $this->get('mailer')->send($message);*/

            return new Response($subscriptionAdapter->verifyWebhook($request->query->get('bt_challenge')));
        }

        return new Response('Method not allowed', 403);
    }
}