<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/23/14
 * Time: 11:11 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Tests\Collection;
use ActiveLAMP\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\TaxonomyBundle\Collection\RelatedEntityCollection;

/**
 * Class RelatedEntityCollectionTest
 *
 * @package ActiveLAMP\TaxonomyBundle\Tests\Collection
 * @author Bez Hermoso <bez@activelamp.com>
 */
class RelatedEntityCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAlwaysReturnsEntities()
    {

        $defs = array(
            __NAMESPACE__ . '\\DummyEntity' => new DummyEntity(),
            __NAMESPACE__ . '\\DummyEntityTwo' => new DummyEntityTwo(),
            __NAMESPACE__ . '\\DummyEntityThree' => new DummyEntityThree(),
        );

        $eTerms = array();

        foreach ($defs as $def) {
            $e = new EntityTerm();
            $e->setEntity($def);
            $eTerms[] = $e;
        }

        $coll = new RelatedEntityCollection($eTerms);

        foreach ($coll as $entity) {
            $class = get_class($entity);

            $this->assertArrayHasKey($class, $defs);

            if (isset($defs[$class])) {
                $this->assertSame($defs[$class], $entity);
            }
        }
    }
}



class DummyEntity
{

}

class DummyEntityTwo
{

}

class DummyEntityThree
{

}