<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 4:43 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Metadata;
use ActiveLAMP\TaxonomyBundle\Entity\MultipleVocabularyField;


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
     * @var array|Vocabulary[]
     */
    protected $vocabularies = array();

    public function __construct(\ReflectionClass $refClass, $type = null, $identifier = null, array $vocabularies = array())
    {
        $this->reflectionClass = $refClass;
        $this->type = $type;
        $this->identifier = $identifier;
        $this->vocabularies = $vocabularies;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param $identifier
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @param array $vocabularies
     */
    public function setVocabularies(array $vocabularies)
    {
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

    public function getVocabularyByName($name)
    {
        foreach ($this->vocabularies as $vocabulary) {
            if ($vocabulary->getName() === $name) {
                return $vocabulary;
            }
        }

        return null;
    }

    /**
     * @param $entity
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function extractIdentifier($entity)
    {
        if (isset($entity->{$this->getIdentifier()})) {
            return $entity->{$this->getIdentifier()};
        }

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

    /**
     * @param $entity
     * @return array|MultipleVocabularyField[]
     */
    public function extractVocabularyFields($entity)
    {
        $fields = array();
        foreach ($this->vocabularies as $vocabulary) {
            $fields[] = $vocabulary->extractValueInField($entity);
        }
        return $fields;
    }
}