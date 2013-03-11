<?php

namespace UJM\ExoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', 'text')
            ->add('url', 'text')
            ->add('type', 'text')

            //->add('user', new UserType())
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UJM\ExoBundle\Entity\Document'
        ));
    }

    public function getName()
    {
        return 'ujm_exobundle_documenttype';
    }
}
