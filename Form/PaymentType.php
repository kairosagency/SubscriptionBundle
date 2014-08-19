<?php

namespace Kairos\SubscriptionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

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
                ->add('cvv', 'encrypted_input', array(
                        'required' => false
                    ));

        $builder
            ->add('expiration_date', 'text', array(
                    'attr' => array(
                        'placeholder' => 'MM/YY'
                    ),
                    'constraints' => array(
                        new NotBlank(),
                        new Regex(array(
                            'pattern' => '/^[0-3][0-9]\/(?:\d{4}|\d{2})$/',
                        )),
                    ),
                ))
            ->add('cardholder_name', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                    ),
                ))
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
                'number'        => '',
                'cvv'           => '',
                'data_class'    => $this->class
        ));
    }

    public function getName()
    {
        return 'kairos_subscription_payment_form';
    }
}