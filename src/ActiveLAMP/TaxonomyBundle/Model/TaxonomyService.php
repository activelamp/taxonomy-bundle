<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 5:19 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Model;
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
    }

    /**
     *
     * @param $terms
     * @param object|string|null $entity Entity object or entity identifier.
     * @throws \InvalidArgumentException
     * @return array
     */
    public function findEntityTermsByTerms($terms, $entity = null)
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

        $qb->select('eterm.entityIdentifier');
        $qb->innerJoin('eterm.tag', 'tag');

        if (is_array($terms)) {
            $qb->andWhere($qb->expr()->in('tag.id', $terms));
        } else {
            $qb->andWhere('tag.id = :terms')
               ->setParameter('terms', $terms);
        }

        if (null !== $metadata) {
            $qb->andWhere('eterm.entityType = :type')
               ->setParameter('type', $metadata->getType());
        }

        return $qb->getQuery()->getResult();
    }
}