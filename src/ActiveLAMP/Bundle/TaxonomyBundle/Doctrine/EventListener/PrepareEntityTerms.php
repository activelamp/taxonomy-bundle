<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/9/14
 * Time: 8:19 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\EventListener;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\AbstractTaxonomyService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;


/**
 * Class PrepareEntityTerms
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\EventListener
 * @author Bez Hermoso <bez@activelamp.com>
 */
class PrepareEntityTerms implements EventSubscriber
{

    /**
     * @var \ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\AbstractTaxonomyService
     */
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
            Events::prePersist,
        );
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        if (!$entity instanceof EntityTerm) {
            return;
        }

        $relEntity = $entity->getEntity();

        $metadata = $this->service->getMetadata()->getEntityMetadata($relEntity);
        $id = $metadata->extractIdentifier($relEntity);
        $entity->setEntityIdentifier($id);
        $entity->setEntityType($metadata->getType());

    }
}