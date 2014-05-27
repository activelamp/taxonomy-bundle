<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/27/14
 * Time: 10:51 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Serializer;
use ActiveLAMP\TaxonomyBundle\Entity\Term;


/**
 * Class JsonSerializer
 *
 * @package ActiveLAMP\TaxonomyBundle\Serializer
 * @author Bez Hermoso <bez@activelamp.com>
 */
class JsonSerializer extends ArraySerializer
{
    public function serialize($entity)
    {
        return json_encode(parent::serialize($entity));
    }

    public function deserialize($entity, $serializedData)
    {
        parent::deserialize($entity, json_decode($serializedData, true));
    }

} 