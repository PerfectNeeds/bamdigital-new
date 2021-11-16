<?php

namespace PN\Bundle\SeoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SeoType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('title')
                ->add('slug')
                ->add('metaDescription')
                ->add('focusKeyword')
                ->add('state')
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'PN\Bundle\SeoBundle\Entity\Seo'
        ));
    }

    public function getBlockPrefix() {
        return 'seoType';
    }

}
