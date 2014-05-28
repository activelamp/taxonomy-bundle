<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 5:19 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Model;
use ActiveLAMP\TaxonomyBundle\Doctrine\QueryInjector;
use ActiveLAMP\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\TaxonomyBundle\Entity\RelatedEntityCollection;
use ActiveLAMP\TaxonomyBundle\Entity\Vocabulary;
use ActiveLAMP\TaxonomyBundle\Entity\VocabularyField;
use ActiveLAMP\TaxonomyBundle\Metadata\TaxonomyMetadata;
use Doctrine\ORM\EntityManager;


/**
 * Class TaxonomyService
 *
 * @package ActiveLAMP\TaxonomyBundle\Model
 * @author Bez Hermoso <bez@activelamp.com>
 */
class TaxonomyService 
{
    protected $em;

    protected $metadata;

    public function __construct(EntityManager $em, TaxonomyMetadata $metadata)
    {
        $this->em = $em;
        $this->metadata = $metadata;
        $this->queryInjector = new QueryInjector();
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
     * @param $name
     * @return Vocabulary
     */
    public function findVocabulary($name)
    {
        return $this->em->getRepository('ALTaxonomyBundle:Vocabulary')->findOneBy(array(
            'name' => $name
        ));
    }

    /**
     * @param $vocabulary
     * @return \ActiveLAMP\TaxonomyBundle\Entity\Term[]|array
     */
    public function findTermsInVocabulary($vocabulary)
    {
        if (is_string($vocabulary)) {
            $vocabulary = $this->findVocabulary($vocabulary);
        }

        return $this->em->getRepository('ALTaxonomyBundle:Term')->findBy(array(
            'vocabulary' => $vocabulary
        ));
    }

    /**
     * @param $id
     * @return \ActiveLAMP\TaxonomyBundle\Entity\Term
     */
    public function findTermById($id)
    {
        return $this->em->getRepository('ALTaxonomyBundle:Term')->find($id);
    }

    public function findTermsByIds(array $ids)
    {
        if (count($ids) == 0) {
            throw new \OutOfBoundsException('Expects an array containing at least one element. Empty array given.');
        }

        $qb = $this->em
                    ->getRepository('ALTaxonomyBundle:Term')
                    ->createQueryBuilder('t');

        $qb->andWhere($qb->expr()->in('t.id', $ids));

        return $qb->getQuery()->getResult();
    }

    /**
     *
     * @param $terms
     * @param object|string|null $entity Entity object or entity identifier.
     * @throws \InvalidArgumentException
     * @return array|object
     */
    public function findEntitiesByTerms($terms, $entity = null)
    {

        $metadata = null;

        if (null !== $entity) {
            $metadata = $this->metadata->getEntityMetadata($entity);
        }

        $qb = $this->em->getRepository('ALTaxonomyBundle:EntityTerm')->createQueryBuilder('eterm');

        $qb->innerJoin('eterm.term', 'term');

        if (is_array($terms)) {
            $qb->andWhere($qb->expr()->in('term.id', $terms));
        } else {
            $qb->andWhere('term.id = :terms')
               ->setParameter('terms', $terms);
        }

        if (null !== $metadata) {
            $qb->andWhere('eterm.entityType = :type')
               ->setParameter('type', $metadata->getType());
        }

        return RelatedEntityCollection::create($qb->getQuery()->getResult());
    }

    public function saveTaxonomies($entity, $persist = true)
    {
        $metadata = $this->metadata->getEntityMetadata($entity);

        $fields = $metadata->extractVocabularyFields($entity);

        $mods = 0;

        foreach ($fields as $field) {

            $inserts = $field->getInsertDiff();
            $deletes = $field->getDeleteDiff();

            /** @var $eTerm EntityTerm */
            foreach ($inserts as $eTerm) {
                $eTerm->setEntity($entity);
                $this->saveEntityTerm($eTerm, false);
                $mods++;
            }

            foreach ($deletes as $eTerm) {
                $this->em->remove($eTerm);
                $mods++;
            }
        }

        if ($mods > 0 && $persist) {
            $this->em->flush();
        }

    }

    /**
     * @param $entity
     * @throws \RuntimeException
     */
    public function loadVocabularyFields($entity)
    {
        $metadata = $this->metadata->getEntityMetadata($entity);

        foreach ($metadata->getVocabularies() as $vocabularyMetadata) {

            $field = $vocabularyMetadata->extractValueInField($entity);

            $coll = null;

            if ($field !== null) {
                $coll = $field;
            }

            $reflectionProperty = $vocabularyMetadata->getReflectionProperty();

            /** @var $vocabulary Vocabulary */
            $vocabulary = $this->em
                               ->getRepository('ALTaxonomyBundle:Vocabulary')
                               ->findOneBy(array('name' => $vocabularyMetadata->getName()));

            if (!$vocabulary) {
                throw new \RuntimeException(
                    sprintf(
                        'Cannot find "%s" vocabulary. Cannot link to %s::%s',
                        $vocabularyMetadata->getName(),
                        $metadata->getReflectionClass()->getName(),
                        $reflectionProperty->getName()
                    ));
            }

            $vocabularyField =
                new VocabularyField(
                    $vocabulary,
                    $this->em,
                    $metadata,
                    $metadata->extractIdentifier($entity),
                    $coll
                );

            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($entity, $vocabularyField);
            $reflectionProperty->setAccessible(false);
        }
    }
}