<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/23/14
 * Time: 2:46 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Form;

use ActiveLAMP\Bundle\TaxonomyBundle\Form\DataTransformer\SingularVocabularyFieldTransformer;
use ActiveLAMP\Bundle\TaxonomyBundle\Model\TaxonomyService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * Class PluralVocabularyFieldType
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Form
 * @author Bez Hermoso <bez@activelamp.com>
 */
class PluralVocabularyFieldType extends AbstractType
{
    protected $service;

    public function __construct(TaxonomyService $service)
    {
        $this->service = $service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new SingularVocabularyFieldTransformer($this->service);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'plural_vocabulary_field';
    }
}