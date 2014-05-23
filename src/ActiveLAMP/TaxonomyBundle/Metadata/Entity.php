<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 4:43 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Metadata;


/**
 * Class Entity
 *
 * @package ActiveLAMP\TaxonomyBundle\Metadata
 * @author Bez Hermoso <bez@activelamp.com>
 */
class Entity 
{
    /**
     * @var \ReflectionClass
     */
    protected $reflectionClass;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var array
     */
    protected $vocabularies = array();

    public function __construct(\ReflectionClass $refClass, $type, $identifier, array $vocabularies = array())
    {
        $this->reflectionClass = $refClass;
        $this->type = $type;
        $this->identifier = $identifier;
        $this->vocabularies = $vocabularies;
    }

    /**
     * @return \ReflectionClass
     */
    public function getReflectionClass()
    {
        return $this->reflectionClass;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Vocabulary $vocabulary
     */
    public function addVocabulary(Vocabulary $vocabulary)
    {
        if (false === array_search($vocabulary, $this->vocabularies)) {
            $this->vocabularies[] = $vocabulary;
        }
    }

    /**
     * @return array|Vocabulary[]
     */
    public function getVocabularies()
    {
        return $this->vocabularies;
    }

    /**
     * @param $entity
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function extractIdentifier($entity)
    {
        $refClass = $this->reflectionClass;

        if (!$refClass->isInstance($entity)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected instance of "%s". "%s" given.',
                $refClass->getName(),
                get_class($entity)
            ));
        }
        $identifierProperty = $refClass->getProperty($this->getIdentifier());
        $identifierProperty->setAccessible(true);
        $id = $identifierProperty->getValue($entity);
        $identifierProperty->setAccessible(false);

        return $id;
    }
}