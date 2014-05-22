<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 2:31 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Entity;

/**
 * Class RelatedEntityCollection
 *
 * @package ActiveLAMP\TaxonomyBundle\Entity
 * @author Bez Hermoso <bez@activelamp.com>
 */
class RelatedEntityCollection extends \ArrayIterator
{
    protected $items;

    /**
     * An array|iterator of EntityTerm instances.
     *
     * @param $entityTerms
     * @throws \InvalidArgumentException
     */
    public function __construct($entityTerms)
    {
        if (!is_array($entityTerms)) {
            throw new \InvalidArgumentException(sprintf('Expected array, "%s" given.', gettype($entityTerms)));
        }

        $this->items = $entityTerms;
    }


    /**
     *
     * Returns the entity term.
     *
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @throws \RuntimeException
     * @return mixed Can return any type.
     */
    public function current()
    {
        $entityTerm = current($this->items);

        if ($entityTerm instanceof EntityTerm) {
            return $entityTerm->getEntity();
        } else {
            throw new \RuntimeException('Collection must only contain instances of ActiveLAMP\TaxonomyBundle\Entity\EntityTerm.');
        }
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