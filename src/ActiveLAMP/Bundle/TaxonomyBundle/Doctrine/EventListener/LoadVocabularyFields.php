<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 4:33 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\EventListener;

use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\TaxonomyMetadata;
use ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\AbstractTaxonomyService;
use ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\TaxonomyService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class LoadVocabularyFields
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\EventListener
 * @author Bez Hermoso <bez@activelamp.com>
 */
class LoadVocabularyFields implements EventSubscriber
{
    /**
     * @var TaxonomyMetadata
     */
    protected $metadata;

    /**
     * @var AbstractTaxonomyService
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
            Events::postLoad
        );
    }

    /**
     * @return TaxonomyMetadata
     */
    protected function getMetadata()
    {
        if (null === $this->metadata) {
            $this->metadata = $this->service->getMetadata();
        }

        return $this->metadata;
    }

    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        $metadata = $this->getMetadata();

        if (!$metadata->hasEntityMetadata($entity)) {
            return;
        }

        $this->service->loadVocabularyFields($entity);

    }
}