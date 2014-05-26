# Zoho invoice connector bundle

This php 5.4+ bundle is aimed at easing the integration of zoho invoice api in a symfony2 app.

It currently handles, contacts / customers, items and invoices.

## Bundle philosophy

This bundle was create to delegate our app invoice management to zoho invoice.
We wanted to connect stuff from our backoffice to zoho invoice for example :
* our users are connected to zoho contacts
* our plans are connected to zoho items

In order to achieve that we built a collection of traits that include all the needed elements in your local database
and implemented lifecycle events to synchronize local object with zoho remote objects.

When you'll persist a connected entity (eg: an user), the bundle will make an api call to create a remote object (eg : a contact) on zoho side,
then it will get the zoho object id and store this reference in your database.

Each time you'll make changes to your entity, it will be synced with zoho.
When an api call fails, the error messages are logged as error in your logs and the corresponding entity
has a "synced" flag set to false (if the api call is successful, the flag is set tu true).

## Bundle usage


### Bundle setup

Add first in your config.yml these informations :

```
kairos_zoho_invoice_connector:
    auth_token: your auth token (mandatory)
    organization_id: your org id (mandatory)
    default_tax_id: default tax id (optional)
```

Then add your bundle in your AppKernel.php

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            ...
            new Kairos\BraintreeSubscriptionBundle\KairosBraintreeSubscriptionBundle(),
            ...
        );
    ....
    }
}
```


### Entities setup

In your *user* entity which should correspond to your customer, add the corresponding trait :


```php
namespace Acme\AcmeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kairos\BraintreeSubscriptionBundle\Model as BraintreeSubscription;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User
{
    use BraintreeSubscription\Contact\ContactConnector;

    ....
}

```


In your *product* entity which should correspond to zoho items, add the corresponding trait :


```php
namespace Acme\AcmeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kairos\BraintreeSubscriptionBundle\Model as BraintreeSubscription;

/**
 * @ORM\Table(name="product")
 * @ORM\Entity
 */
class Product
{
    use BraintreeSubscription\Item\ItemConnector;

    ....
}

```


In your *invoice* entity which should correspond to zoho invoices, add the corresponding trait :


```php
namespace Acme\AcmeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kairos\BraintreeSubscriptionBundle\Model as BraintreeSubscription;

/**
 * @ORM\Table(name="invoice")
 * @ORM\Entity
 */
class Invoice
{
    use BraintreeSubscription\Invoice\InvoiceConnector;

    ....
}

```


To create an invoice :

```php

// get a product
$product = .....


// get a customer
$user = ...

// create an invoice
$invoice  = new Invoice();

$invoice
    ->addItem(array('item_id' => $product->getZohoItemId(), 'quantity' => 1))
    ->setZohoCustomerId($user->getZohoContactId())
    ->addZohoContactPerson($user->getZohoContactPersonId())
    ->setSendInvoice(true); // will send and email with the invoice to the contact person

// then persist and flush
$em->persist($invoice);
$em->flush();

```


## Synchronization command

Sometimes zoho sync will fail, so your entity will have its "synced" set to false.
You can use symfony command "php app/console kairos:zohoinvoiceconnector:sync" to trigger a manual synchronization
with zoho invoice api on all unsynced entities.


## Todo

* Add tests
* Improve Zoho Api support (through zoho invoice api client)
* support more kind of zoho objects (recurring invoices, taxes, etc.)