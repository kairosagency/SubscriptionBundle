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

            // should send 200 instead of exception since the webhook will continue being sent.
            if(count($subscriptions) == 0)
                return new Response('nok', 200);
                //throw new EntityNotFoundException('Could not find subscription entity');

            $subscription = $subscriptions[0];

            $eventName = $subscriptionAdapter->getSubscriptionEvent($subscription, $webhookNotification);

            // todo : keep that for testing but then delete it
            // debug webhook result
            ob_start();
            var_dump($webhookNotification->kind);
            echo chr(13).chr(13).chr(13);
            var_dump($webhookNotification->subscription->transactions);
            echo chr(13).chr(13).chr(13);
            var_dump($eventName);
            $result = ob_get_clean();
            $this->get('logger')->info($result);

            $message = \Swift_Message::newInstance()
                ->setSubject('braintree webhook')
                ->setFrom('assouad@kairostag.com')
                ->setTo('alexandre.assouad@gmail.com')
                ->setBody($result)
            ;
            $this->get('mailer')->send($message);

            if($eventName) {
                $dispatcher = $this->get('event_dispatcher');
                $this->get('kairos_subscription.subscription.manager')->save($subscription);
                $event =  new SubscriptionEvent($subscription, $webhookNotification);
                $dispatcher->dispatch($eventName, $event);
                return new Response('ok', 200);
            }

            return new Response('nok', 400);

        }
        else if($request->isMethod('GET')) {
            $message = \Swift_Message::newInstance()
                ->setSubject('braintree webhook')
                ->setFrom('faule@kairostag.com')
                ->setTo('assouad@kairosagency.com')
                ->setBody('webhookcheck')
            ;
            $this->get('mailer')->send($message);

            return new Response($subscriptionAdapter->verifyWebhook($request->query->get('bt_challenge')));
        }

        return new Response('Method not allowed', 403);
    }
}