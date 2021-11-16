<?php

namespace PN\Bundle\CMSBundle\Form;

use Doctrine\ORM\EntityRepository;
use PN\Bundle\CMSBundle\Entity\BloggerTag;
use PN\Bundle\SeoBundle\Form\SeoType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BloggerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')
            ->add('publish')
            ->add('seo', SeoType::class)
            ->add('post', PostType::class)
            ->add('bloggerTags', EntityType::class, array(
                'required' => FALSE,
                'multiple' => TRUE,
                'class' => BloggerTag::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('bt')
                        ->where('bt.deleted IS NULL ')
                        ->orderBy('bt.id', 'DESC');
                },
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PN\Bundle\CMSBundle\Entity\Blogger'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pn_bundle_cmsbundle_blogger';
    }


}
