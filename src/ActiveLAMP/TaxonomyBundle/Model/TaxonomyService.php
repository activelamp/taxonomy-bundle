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
     * @throws \LogicException
     */
    public function saveEntityTerm(EntityTerm $entityTerm)
    {
        $entity = $entityTerm->getEntity();

        $metadata = $this->metadata->getEntityMetadata($entity);

        if ($metadata == null) {
            throw new \LogicException('Entity is not registered as a recognized entity in TaxonomyBundle.');
        }

        $identifierField = $metadata->getIdentifier();

        $id = null;

        /**
         * If identifier field is accessible.
         */
        if (isset($entity->$identifierField)) {

            $id = $entity->$identifierField;

        } else {
            /**
             * When the identifier field is not accessible (private or protected), peek at the value via reflection.
             */
            $id = $metadata->extractIdentifier($entity);
        }

        if ($id == null) {
            throw new \LogicException('The entity you wish to tag must be persisted first. Identifier cannot be null or false.');
        }

        $entityTerm->setEntityIdentifier($id)
                   ->setEntityType($metadata->getType());

        $this->em->persist($entityTerm);
        $this->em->flush();

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
            if (!$metadata) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Entity "%s" does not seem to be associated with any taxonomies.',
                        is_object($entity) ? get_class($entity) : $entity
                    ));
            }
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
}