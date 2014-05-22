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

    public function addVocabulary($vocabulary)
    {
        if (false !== array_search($vocabulary, $this->vocabularies)) {
            $this->vocabularies[] = $vocabulary;
        }
    }
}