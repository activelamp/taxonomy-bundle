<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/23/14
 * Time: 1:06 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Iterator;
use ActiveLAMP\TaxonomyBundle\Entity\EntityTerm;


/**
 * Class InnerTermIterator
 *
 * @package ActiveLAMP\TaxonomyBundle\Iterator
 * @author Bez Hermoso <bez@activelamp.com>
 */
class InnerTermIterator extends AbstractInnerMemberIterator
{

    public function extractCurrent($current, $key)
    {
        if ($current instanceof EntityTerm) {
            return $current->getTerm();
        } else {
            throw new \RuntimeException('Collection must only contain instances of ActiveLAMP\TaxonomyBundle\Entity\EntityTerm.');
        }
    }
}