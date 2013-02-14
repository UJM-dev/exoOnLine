<?php

namespace UJM\ExoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CoordsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', 'text')
            ->add('shape', 'text')
            ->add('color', 'text')
            ->add('score_coords', 'integer')
            //->add('interactiongraphic', new InteractionGraphicType())
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UJM\ExoBundle\Entity\Coords'
        ));
    }

    public function getName()
    {
        return 'ujm_exobundle_coordstype';
    }
}
