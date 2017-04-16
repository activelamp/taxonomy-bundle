<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 4:42 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Metadata;


/**
 * Class TaxonomyMetadataSubscriber
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Metadata
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
        if ($this->hasEntityMetadata($entity->getReflectionClass()->getName())) {
            return;
        }

        $this->entityMetadata[] = $entity;
    }

    /**
     * @param $object
     * @throws \InvalidArgumentException
     * @return Entity
     */
    public function getEntityMetadata($object)
    {
        $haystack = $object;

        if (is_object($object)) {
            $haystack = get_class($object);
        }

        if (!$this->hasEntityMetadata($haystack)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a recognized taxonomy entity.', $haystack));
        }

        $metadata = null;
        foreach ($this->entityMetadata as $entity) {
            if ($entity->getReflectionClass()->getName() === $haystack) {
                return $entity;
            }
        }

        return null;
    }

    public function hasEntityMetadata($object)
    {
        $haystack = $object;

        if (is_object($object)) {
            $haystack = get_class($object);
        }

        foreach ($this->entityMetadata as $entity) {
            if ($entity->getReflectionClass()->getName() === $haystack) {
                return true;
            }
        }

        return false;
    }

    public function getAllEntityMetadata()
    {
        return $this->entityMetadata;
    }
}