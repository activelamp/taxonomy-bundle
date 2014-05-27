<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/27/14
 * Time: 1:01 PM
 */

namespace ActiveLAMP\TaxonomyBundle\EventListener;
use ActiveLAMP\TaxonomyBundle\Metadata\Entity;
use ActiveLAMP\TaxonomyBundle\Metadata\Reader\AnnotationReader;
use ActiveLAMP\TaxonomyBundle\Metadata\TaxonomyMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * Class ReadMetadata
 *
 * @package ActiveLAMP\TaxonomyBundle\EventListener
 * @author Bez Hermoso <bez@activelamp.com>
 */
class ReadMetadata implements EventSubscriberInterface
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \ActiveLAMP\TaxonomyBundle\Metadata\TaxonomyMetadata
     */
    protected $metadata;

    public function __construct(EntityManager $em, TaxonomyMetadata $metadata)
    {
        $this->em = $em;
        $this->metadata = $metadata;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onRequest',
        );
    }

    public function onRequest()
    {
        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();

        $reader = new AnnotationReader();

        foreach ($metadatas as $doctrineMeta) {

            if (!$doctrineMeta instanceof ClassMetadata) {
                continue;
            }

            $entityMeta = new Entity($doctrineMeta->getReflectionClass());
            $reader->loadMetadataForClass($doctrineMeta->getReflectionClass()->getName(), $entityMeta);

            if ($entityMeta->getType() && count($entityMeta->getVocabularies()) > 0) {
                $this->metadata->addEntityMetadata($entityMeta);
            }
        }
    }
}