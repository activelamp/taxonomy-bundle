<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 5:14 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Doctrine;
use ActiveLAMP\TaxonomyBundle\Metadata\TaxonomyMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;


/**
 * Class QueryInjector
 *
 * @package ActiveLAMP\TaxonomyBundle\Query
 * @author Bez Hermoso <bez@activelamp.com>
 */
class QueryInjector 
{
    protected $metadata;

    public function __construct(TaxonomyMetadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public function attachConditions(QueryBuilder $qb, $term)
    {

    }
}
