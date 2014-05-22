<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 2:22 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Doctrine\EventListener;
use ActiveLAMP\TaxonomyBundle\Annotations\Entity;
use ActiveLAMP\TaxonomyBundle\Annotations\Vocabulary;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use ActiveLAMP\TaxonomyBundle\Metadata as TaxMetadata;


/**
 * Class TaxonomyMetadataListener
 *
 * @package ActiveLAMP\TaxonomyBundle\Doctrine\EventListener
 * @author Bez Hermoso <bez@activelamp.com>
 */

class TaxonomyMetadataListener implements EventSubscriber
{
    /**
     * @var \ActiveLAMP\TaxonomyBundle\Metadata\TaxonomyMetadata
     */
    protected $metadata;

    public function __construct(TaxMetadata\TaxonomyMetadata $metadata)
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
            Events::loadClassMetadata
        );
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $doctrineMetadata = $eventArgs->getClassMetadata();

        $reader = new AnnotationReader();

        $reflectionClass = $doctrineMetadata->getReflectionClass();

        /* @var $entityMetadata Entity */
        $entityMetadata = $reader->getClassAnnotation($reflectionClass, 'ActiveLAMP\TaxonomyBundle\Annotations\Entity');

        if (!$entityMetadata) {
            return;
        }

        $entity =
            new TaxMetadata\Entity($reflectionClass,
                $entityMetadata->getType() ?: $reflectionClass->getName(), $entityMetadata->getIdentifier());

        foreach ($reflectionClass->getProperties() as $property) {

            if ($property->getDeclaringClass()->getName() != $doctrineMetadata->getName()) {
                continue;
            }

            /* @var $vocab Vocabulary */
            $vocab = $reader->getPropertyAnnotation($property, 'ActiveLAMP\TaxonomyBundle\Annotations\Vocabulary');

            if ($vocab != null) {
                if (!$vocab->getName()) {
                    throw new \DomainException(
                        sprintf(
                            "'name' option for Vocabulary annotation must be specified on %s::$%s",
                            $property->getDeclaringClass()->getName(),
                            $property->getName()
                        ));
                }
                $entity->addVocabulary($vocab);
            }
        }

        $this->metadata->addEntityMetadata($entity);

    }
}