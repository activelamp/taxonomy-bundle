<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/30/14
 * Time: 10:58 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Form;
use ActiveLAMP\TaxonomyBundle\Entity\Vocabulary;
use ActiveLAMP\TaxonomyBundle\Form\DataTransformer\SingularVocabularyFieldTransformer;
use ActiveLAMP\TaxonomyBundle\Model\TaxonomyService;
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
    protected $service;

    protected $vocabularyCache;

    public function __construct(TaxonomyService $service)
    {
        $this->service = $service;
    }

    /**
     * @param $name
     * @throws \OutOfBoundsException
     * @return Vocabulary
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
        $transformer = new SingularVocabularyFieldTransformer($this->service, $options['vocabulary']);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'vocabulary'
        ))
        ->setDefaults(array(
            'taxonomy_service' => null,
            'data_class' => 'ActiveLAMP\\TaxonomyBundle\\Entity\\Term',
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