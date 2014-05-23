<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/23/14
 * Time: 10:38 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Tests\Collection;
use ActiveLAMP\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\TaxonomyBundle\Entity\RelatedTermCollection;
use ActiveLAMP\TaxonomyBundle\Entity\Term;


/**
 * Class RelatedTermCollectionTest
 *
 * @package ActiveLAMP\TaxonomyBundle\Tests\Collection
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

        $coll = new RelatedTermCollection($eTerms);

        foreach ($coll as $term) {
            $this->assertInstanceOf('\ActiveLAMP\TaxonomyBundle\Entity\Term', $term);
        }

    }
}