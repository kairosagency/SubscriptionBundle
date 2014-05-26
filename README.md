# Kairos subscription bundle

This php 5.4+ bundle is aimed at easing the integration of subscription payment in a symfony2 app.

It currently handles braintree suscription platform, but integrating other such as paymill should be really easy.

## What does this bundle do ?

**Create your entities easily**

This bundle include base classes that will help you manage your subscriptions : Plan, Customer, Subscription, Credit card, transaction.
these base classes includes all the information useful to communicate with the remote service.

**Easy syncing with remote service**

Some entities will need to be synced with remote service : customer and plan. That's why this bundle include a special
listener that is in charge of synchronizing your local entities with remote entities.

**Webhooks**

This bundle provides also webhook integration and plug webhook events to symfony2 event dispatcher.
You can get a list of all events in the "KairosSubscriptionEvents" class.

**Payment form**

This bundle integrates encrypted payment form provided by braintree. It should be easy to integrate other payment form methods.

## Bundle usage


### Bundle setup

Add first in your config.yml these information :
Entity classes are important, don't forget them !

```
kairos_subscription:
    classes:
        customer:       Acme\SubscriptionBundle\Entity\Customer
        plan:           Acme\SubscriptionBundle\Entity\Plan
        subscription:   Acme\SubscriptionBundle\Entity\Subscription
        credit_card:    Acme\SubscriptionBundle\Entity\CreditCard
        transaction:    Acme\SubscriptionBundle\Entity\Transaction
    adapter:
        braintree:
            environment:                  env
            merchant_id:                  merchant_id
            public_key:                   public_key
            private_key:                  private_key
            client_side_encryption_key:   cse_key
```

For the time being, only braintree subscription is provided but other providers can be easily added.
The subscription service used is autiomatically selected when set-up in the config.


then add the route to your routing.yml :
```
kairos_subscription:
    resource: "@KairosSubscriptionBundle/Resources/config/routing.xml"
```
The webhook url is : http://yourwebsite.com/subscription/webhook


Finaly add your bundle in your AppKernel.php

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            ...
            new Kairos\SubscriptionBundle\KairosSubscriptionBundle(),
            ...
        );
    ....
    }
}
```


### Entities setup

You've got to create 5 entities by extending your entities with the provided models :
* customer:       Acme\SubscriptionBundle\Entity\Customer
* plan:           Acme\SubscriptionBundle\Entity\Plan
* subscription:   Acme\SubscriptionBundle\Entity\Subscription
* credit card:    Acme\SubscriptionBundle\Entity\CreditCard
* transaction:    Acme\SubscriptionBundle\Entity\Transaction

For the time being you've got to create manually relation mapping between entities :
* Customer < one to many > CreditCard
* Customer < one to one > Subscription
* Subscription < one to one > Plan
* Subscription < one to many > Transaction


```php
namespace Acme\AcmeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="customer")
 * @ORM\Entity
 */
class Customer extends Kairos\SubscriptionBundle\Model\Customer
{
    ....
}

```

etc ...

## Twig functions

If you want to use payment form js lib provided by Braintree, you've got to register this service as a global variable :

```
twig:
     globals:
         kairos_subscription_js: "@kairos_subscription.twig_js_service"
```

then you'll be able to use this function to add in your template the necessary js lib :

 ```
 {% autoescape false %}
 {{ kairos_subscription_js.getScript('your form id') }}
 {% endautoescape %}
 ```


## Todo

* Add tests
* Add more validations
* Add more subscription adapters (paymill ...)
* Auto register relation mapping between entities
* Simplify / refactore some stuff
* Multi subscription adapter support ?