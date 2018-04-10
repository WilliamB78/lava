<?php

namespace App\Form;

use App\Entity\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('nbPlaces', IntegerType::class, [
                'label' => 'Nombre de place',
                'invalid_message' => 'Veuillez selectionner un chiffre correct.',
                'attr' => [
                    'min' => 0,
                ]
            ])

            // Permet d'afficher les attributs si c'est pas un ajout
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $room = $event->getData();
                $form = $event->getForm();
                // Si il y a une salle alors l'affiche
                if ($room && $room->getId()) {
                    $form
                        ->add('state')
                        ->add('commentState', TextType::class,[
                            'constraints' => [
                                new NotBlank()
                            ]
                        ]);
                }
            })

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
