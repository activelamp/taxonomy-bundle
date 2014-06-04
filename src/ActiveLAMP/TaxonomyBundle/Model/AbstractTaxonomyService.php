<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/4/14
 * Time: 9:14 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Model;
use ActiveLAMP\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\TaxonomyBundle\Entity\Term;
use ActiveLAMP\TaxonomyBundle\Entity\Vocabulary;
use ActiveLAMP\TaxonomyBundle\Entity\VocabularyFieldInterface;
use ActiveLAMP\TaxonomyBundle\Metadata\TaxonomyMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;


/**
 * Class AbstractTaxonomyService
 *
 * @package ActiveLAMP\TaxonomyBundle\Model
 * @author Bez Hermoso <bez@activelamp.com>
 */
abstract class AbstractTaxonomyService
{
    protected $vocabularies;

    protected $terms;

    protected $em;

    protected $metadata;

    protected $taxonomizedEntityManager;

    /**
     * @param VocabularyRepositoryInterface $vocabularies
     * @param TermRepositoryInterface $terms
     * @param TaxonomizedEntityManager $entityManager
     * @param EntityManager $em
     * @param TaxonomyMetadata $metadata
     */
    public function __construct(
        VocabularyRepositoryInterface $vocabularies,
        TermRepositoryInterface $terms,
        TaxonomizedEntityManager $entityManager,
        EntityManager $em,
        TaxonomyMetadata $metadata
    ) {
        $this->vocabularies = $vocabularies;
        $this->terms = $terms;
        $this->em = $em;
        $this->metadata = $metadata;
        $this->taxonomizedEntityManager = $entityManager;
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
     * @return mixed
     */
    public function findVocabularyByName($name)
    {
        return $this->vocabularies->findByName($name);
    }


    /**
     * @param $vocabulary
     * @return ArrayCollection
     */
    public function findTermsInVocabulary($vocabulary)
    {
        return new ArrayCollection($this->terms->findByVocabulary($vocabulary));
    }

    /**
     * @param $id
     * @return Term
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
     * @param Term $term
     */
    public function deleteTerm(Term $term)
    {
        $this->em->remove($term);
        $this->em->flush();
    }

    /**
     * @param Term $term
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
     * @param EntityTerm $entityTerm
     * @param bool $flush
     * @throws \LogicException
     */
    public function saveEntityTerm(EntityTerm $entityTerm, $flush = true)
    {
        $entity = $entityTerm->getEntity();
        $metadata = $this->metadata->getEntityMetadata($entity);
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

    public function saveTaxonomies($entity, $persist = true)
    {
        $metadata = $this->metadata->getEntityMetadata($entity);
        $dirty = false;

        foreach ($metadata->getVocabularies() as $vocabMetadata) {

            $field = $vocabMetadata->extractValueInField($entity);

            if (!$field instanceof VocabularyFieldInterface) {
                $this->taxonomizedEntityManager->mountVocabularyField($entity, $vocabMetadata->getName());
                $field = $vocabMetadata->extractValueInField($entity);
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
        }

        if ($dirty === true && $persist === true) {
            $this->em->flush();
        }
    }
}