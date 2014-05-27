<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/27/14
 * Time: 10:36 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Serializer;
use ActiveLAMP\TaxonomyBundle\Entity\Term;
use ActiveLAMP\TaxonomyBundle\Metadata\TaxonomyMetadata;
use ActiveLAMP\TaxonomyBundle\Model\TaxonomyService;


/**
 * Class ArraySerializer
 *
 * @package ActiveLAMP\TaxonomyBundle\Serializer
 * @author Bez Hermoso <bez@activelamp.com>
 */
class ArraySerializer implements SerializerInterface
{
    protected $metadata;

    /**
     * @var TaxonomyService
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

            $field = $vocabMetadata->extractVocabularyField($entity);

            $vocabData = array(
                'id' => $field->getVocabulary()->getId(),
                'name' => $vocabMetadata->getName(),
                'label' => $field->getVocabulary()->getLabelName(),
                'description' => $field->getVocabulary()->getDescription(),
                'terms' => array()
            );

            /** @var $term Term */
            foreach ($field as $term) {
                $vocabData['terms'][] = array(
                    'id' => $term->getId(),
                    'name' => $term->getName(),
                    'weight' => $term->getWeight(),
                );
            }

            $serialized[] = $vocabData;
        }

        return $serialized;
    }

    public function setTaxonomyService(TaxonomyService $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
        return $this;
    }

    /**
     * @return TaxonomyService
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

            $field = $vocabMetadata->extractVocabularyField($entity);

            $termIds = array();

            foreach ($vocabData['terms'] as $i => $termData) {
                if (!isset($termData['id'])) {
                    throw new \OutOfBoundsException('Cannot find "id" attribute of term item on index ' . $i);
                }

                $termIds[] = $termData['id'];
            }

            $terms = $service->findTermsByIds($termIds);
            $field->replace($terms);

        }
    }
} 