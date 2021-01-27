<?php

namespace App\Form;

use App\Entity\ArticleSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,[
                "required" => false,
                "label" => false,
                "attr"  =>[
                    'placeholder'=> 'Search...',
                    'class' => 's-header__search-field'
                ]
            ])
            ->add('category',ChoiceType::class,[
                'choices' => [
                    'Tout' => 'All',
                    'Politique' => 'Politique',
                    'Eléction' => 'Eléction',
                    'Sport' => 'Sport',
                ],
                "label" => false,
                "attr"  =>[
                    'class' => 's-header__search-field mt-3 '
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ArticleSearch::class,
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }
}
