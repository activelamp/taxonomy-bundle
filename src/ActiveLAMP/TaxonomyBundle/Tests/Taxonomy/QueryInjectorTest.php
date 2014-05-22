<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 11:56 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Tests\Taxonomy;
use ActiveLAMP\TaxonomyBundle\Doctrine\QueryInjector;
use Doctrine\ORM\QueryBuilder;


/**
 * Class QueryInjectorTest
 *
 * @package ActiveLAMP\TaxonomyBundle\Tests\Taxonomy
 * @author Bez Hermoso <bez@activelamp.com>
 */
class QueryInjectorTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildingQueryForEntityTermsByTerms()
    {
        $queryBuilder = $this->getMock('\Doctrine\ORM\QueryBuilder');

        $queryBuilder->expects($this->once())
                     ->method('innerJoin')
                     ->with('eterm.tag', 'tag');

        $terms = array(1, 2, 3);

        $queryBuilder->expects($this->any())
                     ->method('andWhere')
                     ->with($this->isInstanceOf('\Doctrine\ORM\Query\Expr\Func'));

        $queryBuilder->expects($this->never())
                     ->method('andWhere')
                     ->with('eterm.entityType = :type');

        $queryInjector = new QueryInjector();

        $queryInjector->queryEntityTermsByTerms($queryBuilder, $terms);

    }
}