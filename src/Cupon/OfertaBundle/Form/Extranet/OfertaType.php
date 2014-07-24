<?php

namespace Cupon\OfertaBundle\Form\Extranet;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Cupon\OfertaBundle\Listener\OfertaTypeListener;

class OfertaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('descripcion')
            ->add('condiciones')
            ->add('foto', 'file', array('required' => false))
            ->add('precio', 'money')
            ->add('descuento', 'money')
            ->add('umbral')
        ;

        if (null == $options['data']->getId()) {
            $builder->add('acepto', 'checkbox', array(
                'mapped'   => false,
                'required' => false
            ));
 
            $listener = new OfertaTypeListener();
            $builder->addEventListener(
                FormEvents::PRE_SUBMIT,
                array($listener, 'preSubmit')
            );
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cupon\OfertaBundle\Entity\Oferta'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cupon_ofertabundle_oferta';
    }
}
