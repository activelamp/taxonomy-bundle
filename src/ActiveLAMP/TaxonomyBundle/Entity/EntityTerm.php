<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 4:27 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class EntityTerm
 * @package ActiveLAMP\TaxonomyBundle\Entity
 *
 * @author Bez Hermoso <bez@activelamp.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="entity_tag")
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
     * @ORM\ManyToOne(targetEntity="ActiveLAMP\TaxonomyBundle\Entity\Term", inversedBy="id")
     */
    protected $tag;

    /**
     * @ORM\Column(name="entity_type", type="string", length=100)
     */
    protected $entityType;

    /**
     * @ORM\Column(name="entity_id", type="integer")
     */
    protected $entityIdentifier;

    protected $entity;

    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public function getEntity()
    {
        return $this->entity;
    }
}