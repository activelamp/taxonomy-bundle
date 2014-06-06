<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/23/14
 * Time: 1:06 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Iterator;

use ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm;


/**
 * Class InnerTermIterator
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Iterator
 * @author Bez Hermoso <bez@activelamp.com>
 */
class InnerTermIterator extends AbstractInnerMemberIterator
{

    public function extractCurrent($current, $key)
    {
        if ($current instanceof EntityTerm) {
            return $current->getTerm();
        } else {
            throw new \RuntimeException('Collection must only contain instances of ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm.');
        }
    }
}