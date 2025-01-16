<?php

namespace App\Form;

use App\Entity\Compte;
use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('solde', IntegerType::class, [
                'constraints' => [
                    new NotBlank,
                    new GreaterThanOrEqual([
                        'value' => 10,
                        'message' => 'Le solde doit etre au minimum de 10 euros.',
                    ]),
                ],
            ])
            ->add('numero')
            ->add('type' )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Compte::class,
        ]);
    }
}
