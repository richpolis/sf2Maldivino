<?php

namespace Richpolis\PublicidadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AnuncioType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contenido',null,array(
                'label'=>'Contenido',
                'required'=>true,
                'attr'=>array(
                    'class'=>'cleditor tinymce form-control placeholder',
                   'data-theme' => 'advanced',
                    )
                ))
            ->add('position','hidden')
            ->add('isActive','hidden')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\PublicidadBundle\Entity\Anuncio'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_publicidadbundle_anuncio';
    }
}
