<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/23/14
 * Time: 9:31 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Collection;

use ActiveLAMP\Bundle\TaxonomyBundle\Iterator\InnerTermIterator;
use Traversable;


/**
 * Class RelatedTermCollection
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Entity
 * @author Bez Hermoso <bez@activelamp.com>
 */
class RelatedTermCollection implements \IteratorAggregate
{

    protected $entityTerms;

    public function __construct(array $entityTerms)
    {
        $this->entityTerms = $entityTerms;
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
        return new InnerTermIterator(new \ArrayIterator($this->entityTerms));
    }

    /**
     *
     * @param $entityTerms
     * @throws \InvalidArgumentException
     * @return RelatedEntityCollection
     */
    public static function create($entityTerms)
    {
        if (is_array($entityTerms)) {
            return new self($entityTerms);
        } elseif ($entityTerms instanceof \Iterator || $entityTerms instanceof \IteratorAggregate) {
            return new self(iterator_to_array($entityTerms));
        } else {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cannot create an appropriate iterable object: expects an array or Traversable. "%s" given.',
                    is_object($entityTerms) ? get_class($entityTerms) : gettype($entityTerms)
                ));
        }
    }
}