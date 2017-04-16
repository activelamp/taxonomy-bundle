<?php

namespace ActiveLAMP\Bundle\TaxonomyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Options
 *
 * @ORM\Table(name="taxonomy_term")
 * @ORM\Entity(repositoryClass="ActiveLAMP\Bundle\TaxonomyBundle\Entity\Repository\TermRepository")
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
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary", inversedBy="terms")
     */
    protected $vocabulary;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    protected $label;

    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="integer")
     */
    protected $weight;

    /**
     * @var array|EntityTerm[]
     * @ORM\OneToMany(targetEntity="ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm", mappedBy="term", cascade={"remove"})
     */
    protected $entityTerms;


    public function __construct()
    {
        $this->entityTerms = new ArrayCollection();
        $this->weight = 0;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * @param \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary $vocabulary
     * @return Term
     */
    public function setVocabulary(\ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary $vocabulary = null)
    {
        $this->vocabulary = $vocabulary;
        $vocabulary->addTerm($this);
        return $this;
    }

    /**
     * Get vocabulary
     *
     * @return \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary
     */
    public function getVocabulary()
    {
        return $this->vocabulary;
    }

    /**
     * @return string
     */
    public function getLabelName()
    {
        return $this->label;
    }

    public function setLabelName($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->name;
    }
}