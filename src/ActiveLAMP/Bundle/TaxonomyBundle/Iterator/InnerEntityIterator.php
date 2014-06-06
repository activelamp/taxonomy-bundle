<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/23/14
 * Time: 2:08 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Iterator;

use ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm;


/**
 * Class InnerEntityIterator
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Iterator
 * @author Bez Hermoso <bez@activelamp.com>
 */
class InnerEntityIterator extends AbstractInnerMemberIterator
{

    public function extractCurrent($current, $key)
    {
        if ($current instanceof EntityTerm) {
            return $current->getEntity();
        } else {
            throw new \RuntimeException('Collection must only contain instances of ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm.');
        }
    }
}