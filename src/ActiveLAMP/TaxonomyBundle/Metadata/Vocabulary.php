<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 5:19 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Metadata;
use ActiveLAMP\TaxonomyBundle\Entity\VocabularyField;


/**
 * Class Vocabulary
 *
 * @package ActiveLAMP\TaxonomyBundle\Metadata
 * @author Bez Hermoso <bez@activelamp.com>
 */
class Vocabulary 
{
    /**
     * @var \ReflectionProperty
     */
    protected $field;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param \ReflectionProperty $field
     * @param $name
     */
    public function __construct(\ReflectionProperty $field, $name)
    {
        $this->field = $field;
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFieldName()
    {
        return $this->field->getName();
    }

    public function getReflectionProperty()
    {
        return $this->field;
    }

    /**
     * @param $entity
     * @return VocabularyField
     */
    public function extractVocabularyField($entity)
    {
        $this->field->setAccessible(true);
        $field = $this->field->getValue($entity);
        $this->field->setAccessible(false);

        return $field;
    }
} 