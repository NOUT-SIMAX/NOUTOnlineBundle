<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 20/07/2016
 * Time: 15:23
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Form\Test;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TestRadioContainerType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'a1',
                'test_radio',
                $options+array('mapped'=>false)
            )
            ->add('save', 'submit')
        ;
    }

    public function getName()
    {
        return 'test_radio_container';
    }
}