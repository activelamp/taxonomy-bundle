<?php

namespace ActiveLAMP\Bundle\TaxonomyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TermType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('labelName', 'text', array(
                'label' => 'Label name',
            ))
            ->add('name', 'text', array(
                'label' => 'Machine name',
            ))
            ->add('weight', 'integer')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'activelamp_taxonomybundle_term';
    }
}
