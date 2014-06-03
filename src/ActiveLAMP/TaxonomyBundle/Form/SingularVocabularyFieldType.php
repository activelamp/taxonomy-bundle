<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/30/14
 * Time: 10:58 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Form;
use ActiveLAMP\TaxonomyBundle\Form\DataTransformer\SingularVocabularyFieldTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * Class SingularVocabularyFieldType
 *
 * @package ActiveLAMP\TaxonomyBundle\Form
 * @author Bez Hermoso <bez@activelamp.com>
 */
class SingularVocabularyFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new SingularVocabularyFieldTransformer($options['taxonomy_service']);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'taxonomy_service'
        ))
        ->setDefaults(array(
            'invalid_message' => 'Invalid vocabulary term value.'
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'singular_vocabulary_field';
    }
}