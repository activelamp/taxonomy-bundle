<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/28/14
 * Time: 3:04 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Entity;
use ActiveLAMP\TaxonomyBundle\Metadata\Entity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\UnitOfWork;


/**
 * Class SingularVocabularyField
 *
 * @package ActiveLAMP\TaxonomyBundle\Entity
 * @author Bez Hermoso <bez@activelamp.com>
 */
class SingularVocabularyField extends Term implements VocabularyFieldInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Vocabulary
     */
    protected $vocabulary;

    /**
     * @var Term
     */
    protected $snapshot;

    /**
     * @var \ActiveLAMP\TaxonomyBundle\Metadata\Entity
     */
    protected $metadata;

    /**
     * @var
     */
    protected $identifier;

    /**
     * @var EntityTerm
     */
    protected $entityTerm;

    protected $initialized = false;

    /**
     * @param Vocabulary $vocabulary
     * @param EntityManager $em
     * @param Entity $metadata
     * @param $identifier
     * @param Term $term
     */
    public function __construct(Vocabulary $vocabulary, EntityManager $em, Entity $metadata, $identifier, $term = null)
    {
        $this->vocabulary = $vocabulary;
        $this->em = $em;
        $this->metadata = $metadata;
        $this->identifier = $identifier;
        $this->entityTerm = $term;
    }

    /**
     * @return array|EntityTerm[]
     */
    public function getInsertDiff()
    {
        $this->initialize();
        return $this->entityTerm === $this->snapshot || $this->entityTerm == null ? array() : array($this->entityTerm);
    }

    /**
     * @return array|EntityTerm[]
     */
    public function getDeleteDiff()
    {
        $this->initialize();
        return $this->entityTerm === $this->snapshot || $this->snapshot == null ? array() : array($this->snapshot);
    }

    /**
     * @return Vocabulary
     */
    public function getVocabulary()
    {
        return $this->vocabulary;
    }

    public function initialize()
    {
        if ($this->initialized) {
            return;
        }

        $previous = null;

        if ($this->entityTerm !== null) {
            $previous = $this->entityTerm;
        }

        $qb = $this->em->getRepository('ALTaxonomyBundle:EntityTerm')
                       ->createQueryBuilder('et');

        $qb
            ->innerJoin('et.term', 't')
            ->innerJoin('t.vocabulary', 'v')
            ->addSelect('t')
            ->andWhere('et.entityType = :type')
            ->andWhere('et.entityIdentifier = :id')
            ->andWhere('v.id = :vid')
            ->orderBy('et.id', 'DESC')
            ->setParameters(array(
                'vid' => $this->vocabulary->getId(),
                'id' => $this->identifier,
                'type' => $this->metadata->getType(),
            ))
            ->setMaxResults(1);

        $entityTerm = $qb->getQuery()->getOneOrNullResult();

        $this->snapshot = $entityTerm;

        $this->entityTerm = $entityTerm;

        $this->initialized = true;

        if ($previous !== null) {
            $this->setTerm($previous);
        }

    }

    /**
     * @return Term
     */
    public function getTerm()
    {
        $this->initialize();
        return $this->entityTerm ? $this->entityTerm->getTerm() : null;
    }

    /**
     * @param $term
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setTerm($term = null)
    {

        $this->initialize();

        if (null === $term) {
            $this->entityTerm = null;
            return;
        }

        if (!$term instanceof Term) {
            throw new \InvalidArgumentException(sprintf(
                'Expected instance of Term. "%s" given.',
                get_class($term)
            ));
        }

        if ($this->em->getUnitOfWork()->getEntityState($term) == UnitOfWork::STATE_DETACHED) {
            $term = $this->em->merge($term);
        }

        if ($term->getVocabulary() !== $this->vocabulary) {
            throw new \InvalidArgumentException(sprintf(
                'Term "%s" (#%d) does not belong in "%s" vocabulary.',
                $term->getName(),
                $term->getId(),
                $this->vocabulary->getName()
            ));
        }

        if ($this->entityTerm && $this->entityTerm->getTerm() === $term) {
            return $this;
        }

        $eTerm = new EntityTerm();
        $eTerm->setTerm($term);

        $this->entityTerm = $eTerm;

        return $this;
    }

    public function isInitialized()
    {
        return (boolean) $this->initialized;
    }

    public function getId()
    {
        return $this->getTerm() ? $this->getTerm()->getId() : null;
    }

    public function setName($name)
    {
        if (!$this->getTerm()) {
            return $this;
        }

        $this->getTerm()->setName($name);
        return $this->getTerm();
    }

    public function getName()
    {
        return $this->getTerm() ? $this->getTerm()->getName() : null;
    }

    public function setWeight($weight)
    {
        if (!$this->getTerm()) {
            return $this;
        }

        $this->getTerm()->setWeight($weight);
        return $this->getTerm();
    }

    public function getWeight()
    {
        return $this->getTerm() ? $this->getTerm()->getWeight() : null;
    }

    public function setVocabulary(Vocabulary $vocabulary = null)
    {
        if (!$this->getTerm()) {
            return $this;
        }

        $this->getTerm()->setVocabulary($vocabulary);
        return $this->getTerm();
    }


}