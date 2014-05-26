<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 6:11 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Entity;
use ActiveLAMP\TaxonomyBundle\Iterator\InnerTermIterator;
use ActiveLAMP\TaxonomyBundle\Metadata\Entity;
use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Traversable;


/**
 * Class TermsLazyLoadCollection
 *
 * @package ActiveLAMP\TaxonomyBundle\Entity
 * @author Bez Hermoso <bez@activelamp.com>
 */
class TermsLazyLoadCollection implements Collection
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Entity
     */
    protected $entityMetadata;

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @var mixed
     */
    protected $identifier;

    protected $entity;

    protected $vocabulary;

    /**
     * @var ArrayCollection
     */
    protected $collection;

    /**
     * @param EntityManager $em
     * @param Entity $entityMetadata
     * @param $identifier
     * @param $vocabulary
     */
    public function __construct(EntityManager $em, Entity $entityMetadata, $identifier, $vocabulary)
    {
        $this->em = $em;
        $this->entityMetadata = $entityMetadata;
        $this->identifier = $identifier;
        $this->vocabulary = $vocabulary;
    }

    private function initialize()
    {
        if ($this->initialized) {
            return;
        }

        $terms =
            $this->em->getRepository('ALTaxonomyBundle:Term')
                 ->createQueryBuilder('t')
                 ->innerJoin('t.entityTerms', 'et')
                 ->innerJoin('t.vocabulary', 'v')
                 ->andWhere('et.entityIdentifier = :id')
                 ->andWhere('et.entityType = :type')
                 ->andWhere('v.id = :vid')
                 ->setParameters(array(
                    'id' => $this->identifier,
                    'type' => $this->entityMetadata->getType(),
                    'vid' => $this->vocabulary,
                 ))->getQuery()->getResult();

        $this->collection = new ArrayCollection($terms);
        $this->initialized = true;
    }
}