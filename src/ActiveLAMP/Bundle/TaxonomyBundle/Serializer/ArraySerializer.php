<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/27/14
 * Time: 10:36 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Serializer;

use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\PluralVocabularyField;
use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\TaxonomyMetadata;
use ActiveLAMP\Bundle\TaxonomyBundle\Model\TaxonomyService;


/**
 * Class ArraySerializer
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Serializer
 * @author Bez Hermoso <bez@activelamp.com>
 */
class ArraySerializer
{
    protected $metadata;

    /**
     * @var \ActiveLAMP\Bundle\TaxonomyBundle\Model\TaxonomyService
     */
    protected $taxonomyService;

    public function __construct(TaxonomyMetadata $metadata, TaxonomyService $taxonomyService = null)
    {
        $this->metadata = $metadata;

        if (null !== $taxonomyService) {
            $this->setTaxonomyService($taxonomyService);
        }
    }

    /**
     * @param $entity
     * @return array
     */
    public function serialize($entity)
    {
        $metadata = $this->metadata->getEntityMetadata($entity);

        $serialized = array();

        foreach ($metadata->getVocabularies() as $vocabMetadata) {
            $field = $vocabMetadata->extractValueInField($entity);
            $vocabData = $this->serializeField($field);
            $serialized[] = $vocabData;
        }

        return $serialized;
    }

    public function serializeTerms($terms)
    {
        $termData = array();
        foreach ($terms as $term) {

            if (!$term instanceof Term) {
                throw new \InvalidArgumentException(sprintf(
                    'Expected instance of Term. "%s" given.',
                    get_class($term)
                ));
            }

            $termData[] = $this->serializeTerm($term);
        }

        return $termData;
    }


    public function serializeField(PluralVocabularyField $field)
    {
        $vocabulary = $field->getVocabulary();
        $vocabData = array(
            'id' => $vocabulary->getId(),
            'name' => $vocabulary->getName(),
            'label' => $vocabulary->getLabelName(),
            'description' => $vocabulary->getDescription(),
            'terms' => $this->serializeTerms($field->getTerms()),
        );

        return $vocabData;
    }


    public function setTaxonomyService(TaxonomyService $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
        return $this;
    }

    /**
     * @return \ActiveLAMP\Bundle\TaxonomyBundle\Model\TaxonomyService
     * @throws \RuntimeException
     */
    public function getTaxonomyService()
    {
        if (null === $this->taxonomyService) {
            throw new \RuntimeException('A TaxonomyService was never injected into this serializer.');
        }

        return $this->taxonomyService;
    }

    /**
     * @param $entity
     * @param $serializedData
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @throws \DomainException
     */
    public function deserialize($entity, $serializedData)
    {
        $metadata = $this->metadata->getEntityMetadata($entity);

        $service = $this->getTaxonomyService();
        $service->loadVocabularyFields($entity);

        foreach ($serializedData as $vocabData) {

            if (!isset($vocabData['name'])) {
                throw new \OutOfBoundsException('Cannot find "name" attribute of root element child.');
            }

            if (!isset($vocabData['terms'])) {
                throw new \OutOfBoundsException('Cannot find "terms" attribute of root element child.');
            }

            if (!is_array($vocabData['terms'])) {
                throw new \InvalidArgumentException('"terms" attribute must be of type array.');
            }

            $vocabMetadata = $metadata->getVocabularyByName($vocabData['name']);

            if (!$vocabMetadata) {
                throw new \DomainException(sprintf(
                    'Vocabulary "%s" is not defined in entity type "%s"',
                    $vocabData['name'],
                    $metadata->getType()
                ));
            }

            $field = $vocabMetadata->extractValueInField($entity);

            $terms = $this->deserializeTerms($vocabData['terms']);
            $field->replace($terms);

        }
    }

    public function deserializeTerms(array $terms)
    {
        $termIds = array();
        $terms = array();
        foreach ($terms as $i => $termData) {
            if (!isset($termData['id'])) {
                throw new \OutOfBoundsException('Cannot find "id" attribute of term item on index ' . $i);
            }

            $terms[] = $this->getTaxonomyService()->findTermById($termData['id']);
        }

        $terms = array_filter($terms);
        return $terms;
    }

    public function serializeTerm(Term $term)
    {
        return array(
            'id' => $term->getId(),
            'name' => $term->getName(),
            'label' => $term->getLabelName(),
            'weight' => $term->getWeight(),
        );
    }

    public function deserializeTerm($data)
    {
        if (!isset($data['id'])) {
            throw new \OutOfBoundsException('Expected an "id" attribute.');
        }

        return $this->getTaxonomyService()->findTermById($data['id']);
    }
}