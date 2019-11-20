<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 20/11/2019
 * Time: 22:00
 */

namespace App\Form;


use App\Entity\Persiane;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersianeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codice', TextType::class)
            ->add('pezzi', IntegerType::class)
            ->add('descrizione', TextType::class)
            ->add('salva', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Persiane::class,
        ));
    }

}