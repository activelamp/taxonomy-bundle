<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 4:33 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Doctrine\EventListener;
use ActiveLAMP\TaxonomyBundle\Metadata\TaxonomyMetadata;
use ActiveLAMP\TaxonomyBundle\Model\TaxonomyService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class LoadVocabularyFields
 *
 * @package ActiveLAMP\TaxonomyBundle\Doctrine\EventListener
 * @author Bez Hermoso <bez@activelamp.com>
 */
class LoadVocabularyFields implements EventSubscriber
{

    protected $container;

    protected $metadata;

    protected $service;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
            $this->metadata = $this->container->get('al_taxonomy.metadata');
        }

        return $this->metadata;
    }

    /**
     * @return TaxonomyService
     */
    protected function getService()
    {
        if (null === $this->service) {
            $this->service = $this->container->get('al_taxonomy.taxonomy_service');
        }

        return $this->service;
    }

    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        if (!$this->getMetadata()->hasEntityMetadata($entity)) {
            return;
        }

        $this->getService()->loadVocabularyFields($entity);

    }
}