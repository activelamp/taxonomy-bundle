<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 4:27 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class EntityTerm
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Entity
 *
 * @author Bez Hermoso <bez@activelamp.com>
 *
 * @ORM\Entity(repositoryClass="ActiveLAMP\Bundle\TaxonomyBundle\Entity\Repository\EntityTermRepository")
 * @ORM\Table(name="taxonomy_entity_term")
 */
class EntityTerm
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", unique=true)
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term", inversedBy="entityTerms")
     */
    protected $term;

    /**
     * @ORM\Column(name="entity_type", type="string", length=100)
     */
    protected $entityType;

    /**
     * @ORM\Column(name="entity_id", type="integer")
     */
    protected $entityIdentifier;

    /**
     * @var mixed
     */
    protected $entity;


    /**
     * @param $type
     * @return $this
     */
    public function setEntityType($type)
    {
        $this->entityType = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setEntityIdentifier($id)
    {
        $this->entityIdentifier = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntityIdentifier()
    {
        return $this->entityIdentifier;
    }

    /**
     * @param $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    public function getTerm()
    {
        return $this->term;
    }

    public function setTerm(Term $term)
    {
        $this->term = $term;

        return $this;
    }
}