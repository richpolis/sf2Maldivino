<?php

namespace Richpolis\PublicidadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PublicidadType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcion')
            ->add('imagen')
            ->add('position')
            ->add('isActive')
            ->add('vigencia')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\PublicidadBundle\Entity\Publicidad'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_publicidadbundle_publicidad';
    }
}
