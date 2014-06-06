<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/27/14
 * Time: 12:48 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Metadata\Reader;

use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\Entity;
use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\Vocabulary;
use Doctrine\Common\Annotations\AnnotationReader as Reader;
use ActiveLAMP\Bundle\TaxonomyBundle\Metadata as TaxMetadata;


/**
 * Class AnnotationReader
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Metadata\Reader
 * @author Bez Hermoso <bez@activelamp.com>
 */
class AnnotationReader implements ReaderInterface
{

    public function loadMetadataForClass($className, Entity $metadata)
    {
        $reflectionClass = $metadata->getReflectionClass();
        $reader = new Reader();
        if ($reflectionClass) {
            $this->read($reflectionClass, $metadata, $reader);
            //$this->readMetadata($metadata, $reader, $reflectionClass, $reflectionClass);
        }
    }

    protected function read(\ReflectionClass $reflectionClass, Entity $metadata, Reader $reader)
    {
        /** @var $entityMetadata \ActiveLAMP\Bundle\TaxonomyBundle\Annotations\Entity */
        $entityMetadata = $reader->getClassAnnotation($reflectionClass, 'ActiveLAMP\Bundle\TaxonomyBundle\Annotations\Entity');

        if (!$entityMetadata) {
            return;
        }

        $metadata->setType($entityMetadata->getType() ?: $reflectionClass->getName());
        $metadata->setIdentifier($entityMetadata->getIdentifier());
        foreach ($reflectionClass->getProperties() as $property) {
            /* @var $vocab \ActiveLAMP\Bundle\TaxonomyBundle\Metadata\Vocabulary */
            $vocab = $reader->getPropertyAnnotation($property, 'ActiveLAMP\Bundle\TaxonomyBundle\Annotations\Vocabulary');
            if ($vocab != null) {
                if (!$vocab->getName()) {
                    throw new \DomainException(
                        sprintf(
                            "'name' option for Vocabulary annotation must be specified on %s::$%s",
                            $property->getDeclaringClass()->getName(),
                            $property->getName()
                        ));
                }

                $vocabulary = new Vocabulary($property, $vocab->getName(), $vocab->isSingular());
                $metadata->addVocabulary($vocabulary);
            }
        }

    }

    protected function readMetadata(
        Entity $metadata,
        Reader $reader,
        \ReflectionClass $reflectionClass,
        \ReflectionClass $immediateReflectionClass
    ) {

        /** @var $entityMetadata \ActiveLAMP\Bundle\TaxonomyBundle\Annotations\Entity */
        $entityMetadata = $reader->getClassAnnotation($reflectionClass, 'ActiveLAMP\Bundle\TaxonomyBundle\Annotations\Entity');

        if (!$entityMetadata && !$reflectionClass->getParentClass()) {
            return null;
        } elseif (!$entityMetadata && $reflectionClass->getParentClass()) {
            return $this->readMetadata($metadata, $reader, $reflectionClass->getParentClass(), $immediateReflectionClass);
        }

        $metadata->setType($entityMetadata->getType() ?: $immediateReflectionClass->getName());
        $metadata->setIdentifier($entityMetadata->getIdentifier());

        foreach ($immediateReflectionClass->getProperties() as $property) {

            /* @var $vocab \ActiveLAMP\Bundle\TaxonomyBundle\Metadata\Vocabulary */
            $vocab = $reader->getPropertyAnnotation($property, 'ActiveLAMP\Bundle\TaxonomyBundle\Annotations\Vocabulary');

            if ($vocab != null) {
                if (!$vocab->getName()) {
                    throw new \DomainException(
                        sprintf(
                            "'name' option for Vocabulary annotation must be specified on %s::$%s",
                            $property->getDeclaringClass()->getName(),
                            $property->getName()
                        ));
                }

                $vocabulary = new \ActiveLAMP\Bundle\TaxonomyBundle\Metadata\Vocabulary($property, $vocab->getName(), $vocab->isSingular());
                $metadata->addVocabulary($vocabulary);
            }
        }
    }
}