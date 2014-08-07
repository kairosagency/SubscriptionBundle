<?php

namespace Kairos\SubscriptionBundle;


/**
 * List all available events
 *
 * Class KairosSubscriptionEvents
 * @package Kairos\SubscriptionBundle
 */

final class KairosSubscriptionEvents
{

    const SUBSCRIPTION_CANCELED = 'kairos_subscription.event.subscription.canceled';

    const SUBSCRIPTION_CHARGED_SUCCESSFULLY = 'kairos_subscription.event.subscription.charged_successfully';

    const SUBSCRIPTION_CHARGED_UNSUCCESSFULLY = 'kairos_subscription.event.subscription.charged_unsuccessfully';

    const SUBSCRIPTION_EXPIRED = 'kairos_subscription.event.subscription.expired';

    const SUBSCRIPTION_ACTIVED = 'kairos_subscription.event.subscription.actived';

    const SUBSCRIPTION_TRIAL_ENDED = 'kairos_subscription.event.subscription.trial_ended';

    const SUBSCRIPTION_PAST_DUE = 'kairos_subscription.event.subscription.past_due';
}
