<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/23/14
 * Time: 9:31 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Entity;


/**
 * Class RelatedTermCollection
 *
 * @package ActiveLAMP\TaxonomyBundle\Entity
 * @author Bez Hermoso <bez@activelamp.com>
 */
class RelatedTermCollection extends \ArrayIterator
{

    public function __construct(array $entityTerms)
    {
        parent::__construct($entityTerms);
    }

    public function current()
    {
        $entityTerm = parent::current();

        if ($entityTerm instanceof EntityTerm) {
            return $entityTerm->getTerm();
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