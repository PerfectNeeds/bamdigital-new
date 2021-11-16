<?php

namespace PN\Bundle\CMSBundle\Form;

use PN\Bundle\CMSBundle\Entity\Banner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BannerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')
            ->add('subTitle',null,['required' => false])
            ->add('placement',ChoiceType::class, array(
                'choices' => Banner::$placements,

            ))
            ->add('textPosition',ChoiceType::class, array(
                'choices' => Banner::$positions,

            ))
            ->add('url',UrlType::class,array('required' => false))
            ->add('text', TextareaType::class,array('label' => 'Banner Text', 'required' => false))
            ->add('openType',NULL, array('label' => 'Open new tab'));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PN\Bundle\CMSBundle\Entity\Banner'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pn_bundle_cmsbundle_banner';
    }


}
