<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/4/14
 * Time: 9:31 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Model;
use ActiveLAMP\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\TaxonomyBundle\Entity\SingularVocabularyField;
use ActiveLAMP\TaxonomyBundle\Entity\Vocabulary;
use ActiveLAMP\TaxonomyBundle\Entity\VocabularyFieldInterface;
use ActiveLAMP\TaxonomyBundle\Metadata\TaxonomyMetadata;
use Doctrine\ORM\EntityManager as ObjectManager;


/**
 * Class EntityManager
 *c
 * @package ActiveLAMP\TaxonomyBundle\Model
 * @author Bez Hermoso <bez@activelamp.com>
 */
class TaxonomizedEntityManager
{
    protected $metadata;

    protected $vocabularies;

    protected $fieldFactory;

    /**
     * @param TaxonomyMetadata $metadata
     * @param VocabularyRepositoryInterface $vocabularies
     * @param VocabularyFieldFactory $factory
     */
    public function __construct(
        TaxonomyMetadata $metadata,
        VocabularyRepositoryInterface $vocabularies,
        VocabularyFieldFactory $factory
    ) {
        $this->metadata = $metadata;
        $this->vocabularies = $vocabularies;
        $this->fieldFactory = $factory;
    }

    /**
     * @param $entity
     * @param $vocabulary
     * @throws \RuntimeException
     */
    public function mountVocabularyField($entity, $vocabulary)
    {
        if ($vocabulary instanceof Vocabulary) {
            $name = $vocabulary->getName();
        } else {
            $name = $vocabulary;
        }

        $metadata = $this->metadata->getEntityMetadata($entity);
        $vocabularyMetadata = $metadata->getVocabularyByName($name);

        $previousValue = $vocabularyMetadata->extractValueInField($entity);

        if ($previousValue instanceof VocabularyFieldInterface) {
            /**
             * Already injected.
             */
            return;
        }

        if (!$vocabulary instanceof Vocabulary) {
            $vocabulary = $this->vocabularies->findByName($name);
            if (!$vocabulary) {
                throw new \RuntimeException(
                    sprintf(
                        'Cannot find "%s" vocabulary. Cannot mount taxonomies to %s::%s',
                        $vocabularyMetadata->getName(),
                        $metadata->getReflectionClass()->getName(),
                        $vocabularyMetadata->getReflectionProperty()->getName()
                    ));
            }
        }

        $field =
            $this->fieldFactory->createVocabularyField(
                $vocabulary,
                $metadata->getType(),
                $metadata->extractIdentifier($entity),
                $previousValue,
                $vocabularyMetadata->isSingular());

        $vocabularyMetadata->setVocabularyField($field, $entity);

    }

    /**
     * @param $entity
     * @throws \RuntimeException
     */
    public function mountVocabularyFields($entity)
    {
        $metadata = $this->metadata->getEntityMetadata($entity);

        foreach ($metadata->getVocabularies() as $vocabularyMetadata) {
            $this->mountVocabularyField($entity, $vocabularyMetadata->getName());
        }
    }
}