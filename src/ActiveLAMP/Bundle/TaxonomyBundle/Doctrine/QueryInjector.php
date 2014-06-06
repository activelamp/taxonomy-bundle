<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 5:14 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Doctrine;

use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\Entity;
use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\TaxonomyMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;


/**
 * Class QueryInjector
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Query
 * @author Bez Hermoso <bez@activelamp.com>
 */
class QueryInjector 
{
    /**
     * @param QueryBuilder $qb
     * @param $terms
     * @param null $entity
     * @throws \InvalidArgumentException
     */
    public function queryEntityTermsByTerms(EntityManager $manager, $terms, $entity = null)
    {
        $qb->select('eterm.entityIdentifier');
        $qb->innerJoin('eterm.tag', 'tag');

        if (is_array($terms)) {
            $qb->andWhere($qb->expr()->in('tag.id', $terms));
        } else {
            $qb->andWhere('tag.id = :terms')->setParameter('terms', $terms);
        }

        $type = $entity;

        if (is_object($type) && $type instanceof Entity) {
            $type = $type->getType();
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Expected an instance of ActiveLAMP\Bundle\TaxonomyBundle\Metadata\Entity or entity type string. "%s" given.',
                get_class($type)
            ));
        }

        if ($type) {
            $qb->andWhere('eterm.entityType = :type')->setParameter('type', $type);
        }

    }
}
