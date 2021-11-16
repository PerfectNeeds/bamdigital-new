<?php

namespace PN\Bundle\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class DocumentType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->
                add('files', FileType::class, array(
                    "required" => FALSE,
                    "attr" => array(
                        "multiple" => "multiple",
                        "accept" => "appllication/pdf, appllication/msword",
                    )
                ))
                ->getForm();
    }

    public function getBlockPrefix() {
        return '';
    }

}
