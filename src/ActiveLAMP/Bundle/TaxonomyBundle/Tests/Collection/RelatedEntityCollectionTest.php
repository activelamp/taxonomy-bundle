<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/6/14
 * Time: 1:55 PM
 */
namespace ActiveLAMP\Bundle\TaxonomyBundle\Tests\Collection;
use ActiveLAMP\Bundle\TaxonomyBundle\Tests\Collection\DummyEntity;
use ActiveLAMP\Bundle\TaxonomyBundle\Tests\Collection\DummyEntityTwo;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\Bundle\TaxonomyBundle\Tests\Collection\DummyEntityThree;

/**
 * Class RelatedEntityCollectionTest
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Tests\Collection
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

        $coll = new \ActiveLAMP\Bundle\TaxonomyBundle\Collection\RelatedEntityCollection($eTerms);

        foreach ($coll as $entity) {
            $class = get_class($entity);

            $this->assertArrayHasKey($class, $defs);

            if (isset($defs[$class])) {
                $this->assertSame($defs[$class], $entity);
            }
        }
    }
}