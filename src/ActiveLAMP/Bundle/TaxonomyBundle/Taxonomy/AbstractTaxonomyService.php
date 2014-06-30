<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/4/14
 * Time: 9:14 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy;

use ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\VocabularyFieldInterface;
use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\MetadataFactory;
use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\Reader\AnnotationReader;
use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\TaxonomyMetadata;
use ActiveLAMP\Bundle\TaxonomyBundle\Model\EntityTermRepositoryInterface;
use ActiveLAMP\Bundle\TaxonomyBundle\Model\TermRepositoryInterface;
use ActiveLAMP\Bundle\TaxonomyBundle\Model\VocabularyRepositoryInterface;
use ActiveLAMP\Bundle\TaxonomyBundle\Model\VocabularyFieldFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;


/**
 * Class AbstractTaxonomyService
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Model
 * @author Bez Hermoso <bez@activelamp.com>
 */
abstract class AbstractTaxonomyService
{
    /**
     * @var VocabularyRepositoryInterface
     */
    protected $vocabularies;

    /**
     * @var TermRepositoryInterface
     */
    protected $terms;

    /**
     * @var EntityTermRepositoryInterface
     */
    protected $entityTerms;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    /**
     * @var TaxonomyMetadata|null
     */
    protected $metadata;

    /**
     * @var TaxonomizedEntityManager
     */
    protected $taxonomizedEntityManager;

    /**
     * @var \ActiveLAMP\Bundle\TaxonomyBundle\Metadata\MetadataFactory
     */
    protected $metadataFactory;

    /**
     * @param ObjectManager $em
     */
    public function __construct(
        ObjectManager $em
    ) {
        $this->em = $em;
        $this->vocabularies =
            $em->getRepository('ALTaxonomyBundle:Vocabulary');
        $this->terms =
            $em->getRepository('ALTaxonomyBundle:Term');
        $this->entityTerms =
            $em->getRepository('ALTaxonomyBundle:EntityTerm');

        $this->metadataFactory =
            new MetadataFactory(new AnnotationReader());
        $this->taxonomizedEntityManager =
            new TaxonomizedEntityManager($this, new VocabularyFieldFactory($em, $this->entityTerms));

    }

    /**
     * @return TaxonomyMetadata
     */
    public function getMetadata()
    {
        if ($this->metadata === null) {
            $this->metadata = $this->metadataFactory->getMetadata($this->em);
        }
        return $this->metadata;
    }

    /**
     * @return mixed
     */
    public function findAllVocabularies()
    {
        return $this->vocabularies->findAll();
    }

    public function findAllTerms()
    {
        return $this->terms->findAll();
    }

    /**
     * @param $name
     * @return \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary
     */
    public function findVocabularyByName($name)
    {
        return $this->vocabularies->findByName($name);
    }


    /**
     * @param $vocabulary
     * @return ArrayCollection|Term[]
     */
    public function findTermsInVocabulary($vocabulary)
    {
        if (is_scalar($vocabulary)) {
            $vocabulary = $this->findVocabularyByName($vocabulary);
        }

        return new ArrayCollection($this->terms->findByVocabulary($vocabulary));
    }

    /**
     * @param $id
     * @return \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term
     */
    public function findTermById($id)
    {
        return $this->terms->findById($id);
    }

    /**
     * @param $name
     * @return Term
     */
    public function findTermByName($name)
    {
        return $this->terms->findByName($name);
    }

    /**
     * @param \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term $term
     */
    public function deleteTerm(Term $term)
    {
        $this->em->remove($term);
        $this->em->flush();
    }

    /**
     * @param \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term $term
     * @throws \DomainException
     */
    public function saveTerm(Term $term)
    {
        if (!$term->getVocabulary()) {
            throw new \DomainException('Term must be assigned to a vocabulary before persisting it.');
        }

        $this->em->persist($term);
        $this->em->flush();
    }

    /**
     * @param Vocabulary $vocabulary
     */
    public function deleteVocabulary(Vocabulary $vocabulary)
    {
        $this->em->remove($vocabulary);
        $this->em->flush();
    }

    /**
     * @param Vocabulary $vocabulary
     */
    public function saveVocabulary(Vocabulary $vocabulary)
    {
        $this->em->persist($vocabulary);
        $this->em->flush();
    }

    /**
     * @param \ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm $entityTerm
     * @param bool $flush
     * @throws \LogicException
     */
    public function saveEntityTerm(EntityTerm $entityTerm, $flush = true)
    {
        $entity = $entityTerm->getEntity();
        $metadata = $this->getMetadata()->getEntityMetadata($entity);
        $id = $metadata->extractIdentifier($entity);

        if ($id == null) {
            throw new \LogicException('The entity you wish to tag must be persisted first. Identifier cannot be null or false.');
        }

        $entityTerm->setEntityIdentifier($id)
                   ->setEntityType($metadata->getType());

        $this->em->persist($entityTerm);

        if ($flush === true) {
            $this->em->flush();
        }
    }

    /**
     * @param $entity
     * @throws \RuntimeException
     */
    public function loadVocabularyFields($entity)
    {
        $this->taxonomizedEntityManager->mountVocabularyFields($entity);
    }

    public function saveTaxonomies($entity, $flush = true)
    {
        $metadata = $this->getMetadata()->getEntityMetadata($entity);
        $dirty = false;

        foreach ($metadata->getVocabularies() as $vocabMetadata) {

            $field = $vocabMetadata->extractValueInField($entity);

            if (!$field instanceof VocabularyFieldInterface) {
                $this->taxonomizedEntityManager->mountVocabularyField($entity, $vocabMetadata->getName());
                $field = $vocabMetadata->extractValueInField($entity);
            }

            if (!$field->isDirty()) {
                continue;
            }

            $inserts = $field->getInsertDiff();
            $deletes = $field->getDeleteDiff();

            /** @var $eTerm EntityTerm */
            foreach ($inserts as $eTerm) {
                $eTerm->setEntity($entity);
                $this->saveEntityTerm($eTerm, false);
                $dirty = true;
            }

            foreach ($deletes as $eTerm) {
                $this->em->remove($eTerm);
                $dirty = true;
            }

            $field->setDirty(false);
        }

        if ($dirty === true && $flush === true) {
            $this->em->flush();
        }
    }

    /**
     * @return ObjectManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }
}