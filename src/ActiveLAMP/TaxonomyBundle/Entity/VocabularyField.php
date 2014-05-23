<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 4:29 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Traversable;


/**
 * Class VocabularyField
 *
 * @package ActiveLAMP\TaxonomyBundle\Entity
 * @author Bez Hermoso <bez@activelamp.com>
 */
class VocabularyField implements \IteratorAggregate
{
    /**
     * @var Term[]
     */
    protected $terms = array();

    protected $vocabulary;

    public function __construct(Vocabulary $vocabulary, $terms)
    {
        $this->vocabulary = $vocabulary;
        $this->terms = $terms;
    }

    public function getVocabulary()
    {
        return $this->vocabulary;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        if (is_array($this->terms)) {

            return new ArrayCollection($this->terms);

        } elseif ($this->terms instanceof \Iterator) {

            return $this->terms;

        } elseif ($this->terms instanceof \IteratorAggregate) {

            return $this->terms->getIterator();

        }
    }
}