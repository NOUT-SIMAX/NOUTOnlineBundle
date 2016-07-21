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

class TestRadioType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'Choix',
                'choice',
                array(
                    'expanded'=>true,
                    'choices' => array(
                        '1' => '<Tous>',
                        '2' => 'Vrai',
                        '3' => 'Faux'
                    ),
                    'property_path'=>'Choix',
                )
            )
        ;
    }

    public function getName()
    {
        return 'test_radio';
    }
}