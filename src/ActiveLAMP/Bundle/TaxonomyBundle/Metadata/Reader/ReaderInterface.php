<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/27/14
 * Time: 12:46 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Metadata\Reader;
use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\Entity;


/**
 * Interface ReaderInterface
 * 
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Metadata\Reader
 * @author Bez Hermoso <bez@activelamp.com>
 */
interface ReaderInterface 
{
    public function loadMetadataForClass($className, Entity $metadata);
} 