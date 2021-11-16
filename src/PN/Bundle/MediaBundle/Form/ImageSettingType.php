<?php

namespace PN\Bundle\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PN\Bundle\MediaBundle\Entity\ImageSetting;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ImageSettingType extends AbstractType {

    private $isSuperUser;

    public function __construct($isSuperUser = FALSE) {
        $this->isSuperUser = $isSuperUser;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $this->isSuperUser = $options['isSuperUser'];

        if ($this->isSuperUser == TRUE) {
            $builder
                    ->add('entityName')
                    ->add('backRoute')
                    ->add('uploadPath')
                    ->add('gallery');
        }
        $builder
                ->add('autoResize', NULL, array('required' => false))
                ->add('quality',ChoiceType::class, array(
                    'choices' => array(
                        'Web Resolution (75%)'       => ImageSetting::WEB_RESOLUTION ,
                        'Original Resolution (100%)' =>ImageSetting::ORIGINAL_RESOLUTION ,
                    ),
                    'choices_as_values' => TRUE,

                ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PN\Bundle\MediaBundle\Entity\ImageSetting',
            'isSuperUser' => FALSE,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix() {
        return 'md_bundle_medibundle_imagesetting';
    }

}
