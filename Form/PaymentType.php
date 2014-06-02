<?php

namespace Kairos\SubscriptionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentType extends AbstractType
{
    private $class;

    private $adapterName;


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($this->adapterName == 'braintree')
            $builder
                ->add('number', 'encrypted_input')
                ->add('cvv', 'encrypted_input', array('required' => false));

        $builder
            ->add('expiration_date', 'text', array(
                    'attr' => array(
                        'placeholder' => 'MM/YY'
                    )
                ))
            ->add('cardholder_name', 'text')
            ->add('submit', 'submitbtn')
        ;



    }

    /**
     * @param string $class The credit card class name
     */
    public function __construct($class, $adapterName)
    {
        $this->class = $class;
        $this->adapterName = $adapterName;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class
        ));
    }

    public function getName()
    {
        return 'kairos_subscription_payment_form';
    }
}