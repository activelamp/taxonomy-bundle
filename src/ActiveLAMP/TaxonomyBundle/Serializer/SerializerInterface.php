<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/27/14
 * Time: 10:35 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Serializer;
use ActiveLAMP\TaxonomyBundle\Entity\VocabularyField;


/**
 * Interface SerializerInterface
 *
 * @package ActiveLAMP\TaxonomyBundle\Serializer
 * @author Bez Hermoso <bez@activelamp.com>
 */
interface SerializerInterface
{
    public function serialize($entity);

    public function serializeField(VocabularyField $field);

    public function deserialize($entity, $serializedData);
}