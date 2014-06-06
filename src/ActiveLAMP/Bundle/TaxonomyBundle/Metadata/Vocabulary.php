<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 5:19 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Metadata;

use Doctrine\Common\Collections\ArrayCollection;


/**
 * Class Vocabulary
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Metadata
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
     * @var bool
     */
    protected $singular = false;

    /**
     * @param \ReflectionProperty $field
     * @param $name
     * @param bool $singular
     */
    public function __construct(\ReflectionProperty $field, $name, $singular)
    {
        $this->field = $field;
        $this->name = $name;
        $this->singular = $singular;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFieldName()
    {
        return $this->field->getName();
    }

    public function isSingular()
    {
        return (boolean) $this->singular;
    }

    public function getReflectionProperty()
    {
        return $this->field;
    }

    /**
     * @param $entity
     * @return \ActiveLAMP\Bundle\TaxonomyBundle\Entity\PluralVocabularyField|ArrayCollection
     */
    public function extractValueInField($entity)
    {
        $this->field->setAccessible(true);
        $field = $this->field->getValue($entity);
        $this->field->setAccessible(false);

        return $field;
    }

    public function setVocabularyField($field, $entity)
    {
        $this->field->setAccessible(true);
        $this->field->setValue($entity, $field);
        $this->field->setAccessible(false);
    }
} 