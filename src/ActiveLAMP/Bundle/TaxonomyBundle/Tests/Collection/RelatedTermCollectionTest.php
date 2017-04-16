<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/23/14
 * Time: 10:38 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Tests\Collection;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\Bundle\TaxonomyBundle\Collection\RelatedTermCollection;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term;


/**
 * Class RelatedTermCollectionTest
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Tests\Collection
 * @author Bez Hermoso <bez@activelamp.com>
 */
class RelatedTermCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAlwaysReturnsTerms()
    {
        $eTerms = array();

        foreach (range(1, 5) as $i) {

            $term = new Term();
            $term->setName('Term ' . $i)
                 ->setWeight($i);

            $e = new EntityTerm();
            $e->setTerm($term);

            $eTerms[] = $e;
        }

        $coll = new \ActiveLAMP\Bundle\TaxonomyBundle\Collection\RelatedTermCollection($eTerms);

        foreach ($coll as $term) {
            $this->assertInstanceOf('\ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term', $term);
        }

    }
}