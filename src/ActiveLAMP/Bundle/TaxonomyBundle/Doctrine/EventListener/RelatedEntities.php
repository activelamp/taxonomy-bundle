<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 10:22 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\EventListener;

use ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\TaxonomyMetadata;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;


/**
 * Class RelatedEntities
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\EventListener
 * @author Bez Hermoso <bez@activelamp.com>
 *
 */
class RelatedEntities implements EventSubscriber
{

    /**
     *  @var \ActiveLAMP\Bundle\TaxonomyBundle\Metadata\TaxonomyMetadata
     */
    protected $metadata;

    /**
     * @param \ActiveLAMP\Bundle\TaxonomyBundle\Metadata\TaxonomyMetadata $metadata
     */
    public function __construct(TaxonomyMetadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::postLoad
        );
    }

    /**
     *
     * Loads the appropriate entity object given the entity-type and identifier in an EntityTerm object.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entityTerm = $eventArgs->getEntity();

        if ($entityTerm instanceof EntityTerm) {

            $metadata = $this->metadata->getEntityMetadata($entityTerm->getEntityType());
            $entity = $eventArgs->getEntityManager()
                      ->find($metadata->getReflectionClass()->getName(), $entityTerm->getEntityIdentifier());
            if ($entity) {
                $entityTerm->setEntity($entity);
            }
        }
    }
}