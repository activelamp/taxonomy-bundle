<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/23/14
 * Time: 11:47 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Tests\Collection;
use ActiveLAMP\TaxonomyBundle\Entity\TermsLazyLoadCollection;
use ActiveLAMP\TaxonomyBundle\Metadata\Entity;


/**
 * Class LazyLoadTermsCollectionTest
 *
 * @package ActiveLAMP\TaxonomyBundle\Tests\Collection
 * @author Bez Hermoso <bez@activelamp.com>
 */
class LazyLoadTermsCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testLazyQuerying()
    {
        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
                    ->disableOriginalConstructor()
                    ->getMock();

        $entity = new LazyDummyEntity(1);
        $refClass = new \ReflectionClass($entity);

        $metadata = new Entity($refClass, $refClass->getName(), 'id');

        $coll = new TermsLazyLoadCollection($em, $metadata, 1);

        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
                           ->disableOriginalConstructor()
                           ->getMock();

        $em->expects($this->never())
           ->method('getRepository');
    }

    public function testLazyQueryingTwo()
    {
        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entity = new LazyDummyEntity(1);
        $refClass = new \ReflectionClass($entity);

        $metadata = new Entity($refClass, $refClass->getName(), 'id');

        $id = rand(1, 100);

        $coll = new TermsLazyLoadCollection($em, $metadata, $id);

        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects($this->once())
                   ->method('findBy')
                   ->with($this->equalTo(array(
                        'entityType' => $metadata->getType(),
                        'entityIdentifier' => $id
                    )))->will($this->returnValue(array()));

        $em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('ALTaxonomyBundle:EntityTerm'))
            ->will($this->returnValue($repository));

        foreach ($coll as $terms) {
            //Triggers here.
        }

    }
}

class LazyDummyEntity
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
}