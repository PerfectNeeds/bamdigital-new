<?php

namespace PN\Bundle\SeoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BackLinkType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('word')
                ->add('link', 'url')
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'PN\Bundle\SeoBundle\Entity\BackLink'
        ));
    }

    public function getBlockPrefix() {
        return 'backlink';
    }

}
