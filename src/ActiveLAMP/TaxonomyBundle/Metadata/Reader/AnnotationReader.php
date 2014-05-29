<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/27/14
 * Time: 12:48 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Metadata\Reader;
use ActiveLAMP\TaxonomyBundle\Metadata\Entity;
use Doctrine\Common\Annotations\AnnotationReader as Reader;
use ActiveLAMP\TaxonomyBundle\Metadata as TaxMetadata;


/**
 * Class AnnotationReader
 *
 * @package ActiveLAMP\TaxonomyBundle\Metadata\Reader
 * @author Bez Hermoso <bez@activelamp.com>
 */
class AnnotationReader implements ReaderInterface
{

    public function loadMetadataForClass($className, Entity $metadata)
    {
        $reflectionClass = $metadata->getReflectionClass();
        $reader = new Reader();
        if ($reflectionClass) {
            $this->readMetadata($metadata, $reader, $reflectionClass, $reflectionClass);
        }
    }

    protected function readMetadata(
        Entity $metadata,
        Reader $reader,
        \ReflectionClass $reflectionClass,
        \ReflectionClass $immediateReflectionClass
    ) {

        /** @var $entityMetadata \ActiveLAMP\TaxonomyBundle\Annotations\Entity */
        $entityMetadata = $reader->getClassAnnotation($reflectionClass, 'ActiveLAMP\TaxonomyBundle\Annotations\Entity');

        if (!$entityMetadata && !$reflectionClass->getParentClass()) {
            return null;
        } elseif (!$entityMetadata && $reflectionClass->getParentClass()) {
            return $this->readMetadata($metadata, $reader, $reflectionClass->getParentClass(), $immediateReflectionClass);
        }

        $metadata->setType($entityMetadata->getType() ?: $immediateReflectionClass->getName());
        $metadata->setIdentifier($entityMetadata->getIdentifier());

        foreach ($immediateReflectionClass->getProperties() as $property) {

            /* @var $vocab TaxMetadata\Vocabulary */
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

                $vocabulary = new TaxMetadata\Vocabulary($property, $vocab->getName(), $vocab->isSingular());
                $metadata->addVocabulary($vocabulary);
            }
        }
    }
}