<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/6/14
 * Time: 3:55 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\EventListener;
use ActiveLAMP\Bundle\TaxonomyBundle\Model\AbstractTaxonomyService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;


/**
 * Class PersistTaxonomies
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\EventListener
 * @author Bez Hermoso <bez@activelamp.com>
 */
class PersistTaxonomies implements EventSubscriber
{
    protected $service;

    public function __construct(AbstractTaxonomyService $taxonomyService)
    {
        $this->service = $taxonomyService;
    }
    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::postUpdate,
            Events::postPersist,
        );
    }

    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        if ($this->service->getMetadata()->hasEntityMetadata($entity)) {
            $this->service->saveTaxonomies($entity);
        }
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        if ($this->service->getMetadata()->hasEntityMetadata($entity)) {
            $this->service->saveTaxonomies($entity);
        }
    }
}
