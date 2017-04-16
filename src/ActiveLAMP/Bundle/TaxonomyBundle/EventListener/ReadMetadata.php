<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/27/14
 * Time: 1:01 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\EventListener;

use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\Entity;
use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\Reader\AnnotationReader;
use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\TaxonomyMetadata;
use ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\AbstractTaxonomyService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * Class ReadMetadata
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\EventListener
 * @author Bez Hermoso <bez@activelamp.com>
 */
class ReadMetadata implements EventSubscriberInterface
{
    /**
     * @param AbstractTaxonomyService $service
     */
    public function __construct(AbstractTaxonomyService $service)
    {
        $this->service = $service;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onRequest',
        );
    }

    public function onRequest()
    {
        /**
         * Trigger metadata reading.
         */
        $metadata = $this->service->getMetadata();
    }
}