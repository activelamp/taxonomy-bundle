<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 10:22 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\EventListener;

use ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\AbstractTaxonomyService;
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
    protected $service;

    /**
     * @param \ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\AbstractTaxonomyService $service
     */
    public function __construct(AbstractTaxonomyService $service)
    {
        $this->service = $service;
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

            $metadata = $this->service->getMetadata()->getEntityMetadata($entityTerm->getEntityType());
            $entity = $eventArgs->getEntityManager()
                      ->find($metadata->getReflectionClass()->getName(), $entityTerm->getEntityIdentifier());
            if ($entity) {
                $entityTerm->setEntity($entity);
            }
        }
    }
}