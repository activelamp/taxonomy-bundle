<?php

namespace ActiveLAMP\Bundle\TaxonomyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Vocabulary
 *
 * @ORM\Table(name="taxonomy_vocabulary")
 * @ORM\Entity(repositoryClass="ActiveLAMP\Bundle\TaxonomyBundle\Entity\Repository\VocabularyRepository")
 */
class Vocabulary
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
     * @var string
     *
     * @ORM\Column(name="label_name", type="string", length=255)
     */
    private $labelName;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var ArrayCollection|Term[]
     * @ORM\OneToMany(targetEntity="ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term", mappedBy="vocabulary", cascade={"remove"})
     */
    private $terms;

    public function __construct()
    {
        $this->terms = new ArrayCollection();
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

    /**
     * Set labelName
     *
     * @param string $labelName
     * @return $this
     */
    public function setLabelName($labelName)
    {
        $this->labelName = $labelName;
    
        return $this;
    }

    /**
     * Get labelName
     *
     * @return string 
     */
    public function getLabelName()
    {
        return $this->labelName;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
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
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    function __toString()
    {
        return $this->getName();
    }

    /**
     * @param $name
     * @throws \DomainException
     * @return Term
     */
    public function getTermByName($name)
    {
        /** @var $term Term */
        foreach ($this->terms as $term) {
            if ($term->getName() == $name) {
                return $term;
            }
        }

        throw new \DomainException(sprintf('Cannot find term of name "%s".', $name));
    }

    /**
     * @param Term $term
     */
    public function addTerm(Term $term)
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
        }
    }
}