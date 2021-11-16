<?php

namespace PN\Bundle\CMSBundle\Form;

use Doctrine\ORM\EntityRepository;
use PN\Bundle\SeoBundle\Form\SeoType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')
            ->add('reference',UrlType::class,array('required' => false))
            ->add('publish')
            ->add('post',PostType::class)
            ->add('blogCategory', EntityType::class, [
                'required' => TRUE,
                'class' => "CMSBundle:BlogCategory",
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('bc')
                        ->where('bc.deleted IS NULL ')
                        ->orderBy('bc.id', 'DESC');
                },
            ]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PN\Bundle\CMSBundle\Entity\Blog'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pn_bundle_cmsbundle_blog';
    }


}
