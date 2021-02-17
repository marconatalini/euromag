<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 13/01/2019
 * Time: 14:25
 */

namespace App\Form;

use App\Entity\Articoli;
use App\Entity\Ubicazioni;
use App\Repository\ArticoliRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UbicazioniForm extends AbstractType
{
    private $search;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('codice', HiddenType::class, ['attr' => array('readonly'=>'')])
            ->add('fila', HiddenType::class, ['attr' => array('readonly'=>'')])
            ->add('colonna', HiddenType::class, ['attr' => array('readonly'=>'')])
            ->add('piano', HiddenType::class, ['attr' => array('readonly'=>'')])
            ->add('filtro', TextType::class, ['mapped' => false, 'attr' =>array(
                'id'=>'cerca_articolo',
                'autocomplete' =>'off',
                'autofocus' => '')])
           /* ->add('articolo', EntityType::class, array(
                'class' => Articoli::class,
                'choice_label' => 'codice',
                'placeholder' => 'scegli un profilo da ubicare',
                'attr' => ['type'=>'text']
            ))*/

        ;

        $formModifier = function (FormInterface $form, string $search = null) {

            $this->search = $search;

            if (null == $search) {
                $form->add('articolo', EntityType::class, array(
                    'class' => Articoli::class,
                    'choice_label' => 'codice',
                    'placeholder' => 'scegli un profilo o scrivi qui sopra per filtrare',
                ));
            } else {
                $form->add('articolo', EntityType::class, [
                    'class' => Articoli::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('a')
                            ->where('a.codice like :val')
                            ->setParameter('val', '%'.$this->search.'%')
                            ;
                    },
                    'placeholder' => 'Profili '.$this->search. '...',
                ]);
            }

            $form->add('libera', SubmitType::class, array(
                'attr' =>  ['class' => 'btn-success btn', 'value' => 'libera',]
                ))
                ->add('assegna', SubmitType::class, array(
                    'attr' =>  ['class' => 'btn-danger btn', 'value' => 'assegna',]
                ))
                ->add('aggiungi', SubmitType::class, array(
                    'label' => 'Aggiungi profilo',
                    'attr' =>  ['class' => 'btn-primary btn', 'value' => 'aggiungi', ]
                ));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                if ( isset($_POST['filtro'])){
                    $cerca = $_POST['filtro'];
                } else {
                    $cerca = '';
                }
                $formModifier($event->getForm(), $cerca);
            }
        );

        $builder->get('filtro')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $sport = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $sport);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Ubicazioni::class,
        ));
    }
}