<?php

namespace ActiveLAMP\TaxonomyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Options
 *
 * @ORM\Table(name="taxonomy_term")
 * @ORM\Entity
 */
class Term
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ActiveLAMP\TaxonomyBundle\Entity\Vocabulary", inversedBy="terms")
     * @ORM\JoinColumn(name="vocabulary_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $vocabulary;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="integer")
     */
    private $weight;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Term
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return Term
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    
        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set vocabulary
     *
     * @param \ActiveLAMP\TaxonomyBundle\Entity\Vocabulary $vocabulary
     * @return Term
     */
    public function setVocabulary(\ActiveLAMP\TaxonomyBundle\Entity\Vocabulary $vocabulary = null)
    {
        $this->vocabulary = $vocabulary;
    
        return $this;
    }

    /**
     * Get vocabulary
     *
     * @return \ActiveLAMP\TaxonomyBundle\Entity\Vocabulary 
     */
    public function getVocabulary()
    {
        return $this->vocabulary;
    }

    function __toString()
    {
        return $this->name;
    }
}