<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 4:42 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Metadata;


/**
 * Class TaxonomyMetadataSubscriber
 *
 * @package ActiveLAMP\TaxonomyBundle\Metadata
 * @author Bez Hermoso <bez@activelamp.com>
 */
class TaxonomyMetadata 
{
    /**
     * @var array|Entity[]
     */
    protected $entityMetadata = array();

    public function addEntityMetadata(Entity $entity)
    {
        if ($this->getEntityMetadata($entity->getReflectionClass()->getName())) {
            throw new \RuntimeException("Duplicate metadata entity.");
        }

        $this->entityMetadata[] = $entity;
    }

    /**
     * @param $object
     * @return Entity|null
     */
    public function getEntityMetadata($object)
    {
        $haystack = $object;

        if (is_object($object)) {
            $haystack = get_class($object);
        }

        $metadata = null;
        foreach ($this->entityMetadata as $entity) {
            if ($entity->getReflectionClass()->getName() === $haystack) {
                return $entity;
            }
        }

        return null;
    }

    public function getAllEntityMetadata()
    {
        return $this->entityMetadata;
    }
}