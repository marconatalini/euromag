<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 10/01/2019
 * Time: 00:59
 */

namespace App\Form;

use App\Entity\Articoli;
use App\Entity\Ubicazioni;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class ArticoliForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codice', TextType::class)
            ->add('descrizione', TextType::class)
            ->add('note', TextType::class)
            ->add('salva', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Articoli::class,
        ));
    }
}