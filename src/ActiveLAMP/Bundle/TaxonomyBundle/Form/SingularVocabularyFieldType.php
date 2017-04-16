<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/30/14
 * Time: 10:58 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Form;

use ActiveLAMP\Bundle\TaxonomyBundle\Form\DataTransformer\SingularVocabularyFieldTransformer;
use ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\TaxonomyService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * Class SingularVocabularyFieldType
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Form
 * @author Bez Hermoso <bez@activelamp.com>
 */
class SingularVocabularyFieldType extends AbstractType
{
    protected $service;

    protected $vocabularyCache;

    public function __construct(TaxonomyService $service)
    {
        $this->service = $service;
    }

    /**
     * @param $name
     * @throws \OutOfBoundsException
     * @return \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary
     */
    protected function getVocabulary($name)
    {
        if (isset($this->vocabularyCache[$name])) {
            return $this->vocabularyCache[$name];
        }
        $vocabulary = $this->service->findVocabularyByName($name);
        if (!$vocabulary) {
            throw new \OutOfBoundsException(sprintf('Cannot find vocabulary named "%s"', $name));
        }
        $this->vocabularyCache[$name] = $vocabulary;
        return $vocabulary;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        //$transformer = new SingularVocabularyFieldTransformer($options['taxonomy_service'], $options['vocabulary']);
        //$builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'taxonomy_service' => $this->service,
            'choice_list' => function (Options $options) {
                return new TermChoiceList($options['taxonomy_service'], $options['vocabulary']);
            }
        ));

        $resolver->setRequired(array(
            'vocabulary'
        ))
        ->setAllowedTypes(array(
                'taxonomy_service' => array('ActiveLAMP\\Bundle\\TaxonomyBundle\\Taxonomy\\AbstractTaxonomyService'),
        ))
        ;
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'singular_vocabulary_field';
    }
}