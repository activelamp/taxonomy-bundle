<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 4:29 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Entity;

use ActiveLAMP\Bundle\TaxonomyBundle\Iterator\InnerTermIterator;
use ActiveLAMP\Bundle\TaxonomyBundle\Model\EntityTermRepositoryInterface;
use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\UnitOfWork;
use Traversable;


/**
 * Class PluralVocabularyField
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Entity
 * @author Bez Hermoso <bez@activelamp.com>
 */
class PluralVocabularyField implements VocabularyFieldInterface, Collection
{
    /**
     * @var Term[]
     */
    protected $terms = array();

    /**
     * @var Vocabulary
     */
    protected $vocabulary;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var
     */
    protected $type;

    /**
     * @var mixed
     */
    protected $identifier;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @var array
     */
    protected $snapshot;

    /**
     * @var boolean
     */
    protected $dirty = false;

    /**
     * @var \ActiveLAMP\Bundle\TaxonomyBundle\Model\EntityTermRepositoryInterface
     */
    protected $entityTerms;

    /**
     * @param ObjectManager $em
     * @param \ActiveLAMP\Bundle\TaxonomyBundle\Model\EntityTermRepositoryInterface $entityTerms
     * @param Vocabulary $vocabulary
     * @param $type
     * @param $identifier
     * @param Collection $collection
     */
    public function __construct(
        ObjectManager $em,
        EntityTermRepositoryInterface $entityTerms,
        Vocabulary $vocabulary,
        $type, $identifier,
        Collection $collection = null
    ) {
        $this->em = $em;
        $this->vocabulary = $vocabulary;
        $this->entityTerms = $entityTerms;
        $this->type = $type;
        $this->identifier = $identifier;
        $this->collection = $collection;
    }

    /**
     * @return Vocabulary
     */
    public function getVocabulary()
    {
        return $this->vocabulary;
    }

    public function initialize()
    {
        if ($this->initialized) {
            return;
        }

        $unManaged = array();
        if ($this->collection !== null) {
            $unManaged = $this->collection->toArray();
        }

        $eTerms = $this->entityTerms->findEntities($this->vocabulary->getId(), $this->type, $this->identifier);

        $this->snapshot = $eTerms;
        $this->collection = new ArrayCollection($eTerms);

        $this->initialized = true;

        foreach ($unManaged as $term) {
            $this->add($term);
        }

    }

    /**
     * @return Traversable|Term[]
     */
    public function getTerms()
    {
        return $this->getIterator();
    }

    public function getInsertDiff()
    {
        $this->initialize();
        return array_udiff_assoc(
            $this->collection->toArray(),
            $this->snapshot,
            function ($a, $b) {
                return $a === $b ? 0 : 1;
            }
        );
    }

    public function getDeleteDiff()
    {
        $this->initialize();
        return array_udiff_assoc(
            $this->snapshot,
            $this->collection->toArray(),
            function ($a, $b) {
                return $a === $b ? 0 : 1;
            }
        );
    }

    /**
     * @param $element
     * @throws \InvalidArgumentException
     */
    protected function checkType($element)
    {
        if (!$element instanceof Term) {
            throw new \InvalidArgumentException(sprintf(
                'Expected instance of %s. "%s" given.',
                __NAMESPACE__ . '\\Term',
                get_class($element)
            ));
        }

        if ($this->em->getUnitOfWork()->getEntityState($element) == UnitOfWork::STATE_DETACHED) {
            $element = $this->em->merge($element);
        }

        if ($element->getVocabulary() !== $this->vocabulary) {
            throw new \InvalidArgumentException(sprintf(
                'Term "%s" (#%d) does not belong in the "%s" vocabulary.',
                $element->getName(),
                $element->getId(),
                $this->vocabulary->getName()
            ));
        }
    }

    /**
     * Adds an element at the end of the collection.
     *
     * @param mixed $element The element to add.
     *
     * @throws \InvalidArgumentException
     * @return boolean Always TRUE.
     */
    function add($element)
    {
        $this->checkType($element);

        $this->initialize();

        /** @var $eTerm EntityTerm */
        foreach ($this->collection->toArray() as $eTerm) {
            if ($eTerm->getTerm() === $element) {
                return true;
            }
        }

        $this->setDirty(true);
        $entityTerm = new EntityTerm();
        $entityTerm->setTerm($element);
        return $this->collection->add($entityTerm);
    }

    /**
     * Clears the collection, removing all elements.
     *
     * @return void
     */
    function clear()
    {
        $this->initialize();
        $this->collection->clear();
    }

    /**
     * Checks whether an element is contained in the collection.
     * This is an O(n) operation, where n is the size of the collection.
     *
     * @param mixed $element The element to search for.
     *
     * @return boolean TRUE if the collection contains the element, FALSE otherwise.
     */
    function contains($element)
    {
        $this->checkType($element);
        $this->initialize();

        /** @var $eTerm EntityTerm */
        foreach ($this->collection->toArray() as $eTerm) {
            if ($eTerm->getTerm() === $element) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether the collection is empty (contains no elements).
     *
     * @return boolean TRUE if the collection is empty, FALSE otherwise.
     */
    function isEmpty()
    {
        $this->initialize();
        return $this->collection->isEmpty();
    }

    /**
     * Removes the element at the specified index from the collection.
     *
     * @param string|integer $key The kex/index of the element to remove.
     *
     * @return mixed The removed element or NULL, if the collection did not contain the element.
     */
    function remove($key)
    {
        $this->initialize();
        $element = $this->collection->remove($key);
        if ($element) {
            $this->setDirty(true);
        }
        return $element;
    }

    /**
     * Removes the specified element from the collection, if it is found.
     *
     * @param mixed $element The element to remove.
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    function removeElement($element)
    {
        $this->checkType($element);
        $this->initialize();

        /** @var $eTerm EntityTerm */
        foreach ($this->collection->toArray() as $eTerm) {
            if ($eTerm->getTerm() === $element) {
                $this->setDirty(true);
                return $this->collection->removeElement($eTerm);
            }
        }


        return false;
    }

    /**
     * Checks whether the collection contains an element with the specified key/index.
     *
     * @param string|integer $key The key/index to check for.
     *
     * @return boolean TRUE if the collection contains an element with the specified key/index,
     *                 FALSE otherwise.
     */
    function containsKey($key)
    {
        $this->initialize();
        return $this->collection->containsKey($key);
    }

    /**
     * Gets the element at the specified key/index.
     *
     * @param string|integer $key The key/index of the element to retrieve.
     *
     * @return mixed
     */
    function get($key)
    {
        $this->initialize();
        return $this->collection->get($key);
    }

    /**
     * Gets all keys/indices of the collection.
     *
     * @return array The keys/indices of the collection, in the order of the corresponding
     *               elements in the collection.
     */
    function getKeys()
    {
        $this->initialize();
        return $this->collection->getKeys();
    }

    /**
     * Gets all values of the collection.
     *
     * @return array The values of all elements in the collection, in the order they
     *               appear in the collection.
     */
    function getValues()
    {
        $this->initialize();
        return array_values(iterator_to_array($this->getIterator()));
    }

    /**
     * Sets an element in the collection at the specified key/index.
     *
     * @param string|integer $key The key/index of the element to set.
     * @param mixed $value The element to set.
     *
     * @return void
     */
    function set($key, $value)
    {
        $this->checkType($value);
        $this->initialize();

        $eTerm = new EntityTerm();
        $eTerm->setTerm($value);
        $this->collection->set($key, $eTerm);
    }

    /**
     * Gets a native PHP array representation of the collection.
     *
     * @return array
     */
    function toArray()
    {
        $this->initialize();
        return iterator_to_array($this->getIterator());
    }

    /**
     * Sets the internal iterator to the first element in the collection and returns this element.
     *
     * @return mixed
     */
    function first()
    {
        $this->initialize();
        $first = $this->collection->first();
        return $first->getTerm();
    }

    /**
     * Sets the internal iterator to the last element in the collection and returns this element.
     *
     * @return mixed
     */
    function last()
    {
        $this->initialize();
        $last = $this->collection->last();
        return $last->getTerm();
    }

    /**
     * Gets the key/index of the element at the current iterator position.
     *
     * @return int|string
     */
    function key()
    {
        $this->initialize();
        return $this->collection->key();
    }

    /**
     * Gets the element of the collection at the current iterator position.
     *
     * @return mixed
     */
    function current()
    {
        $this->initialize();
        $current = $this->collection->current();
        return $current->getTerm();
    }

    /**
     * Moves the internal iterator position to the next element and returns this element.
     *
     * @return mixed
     */
    function next()
    {
        $this->initialize();
        $next = $this->collection->next();
        return $next->getTerm();
    }

    /**
     * Tests for the existence of an element that satisfies the given predicate.
     *
     * @param Closure $p The predicate.
     *
     * @return boolean TRUE if the predicate is TRUE for at least one element, FALSE otherwise.
     */
    function exists(Closure $p)
    {
        $this->initialize();
        return $this->collection->exists($p);
    }

    /**
     * Returns all the elements of this collection that satisfy the predicate p.
     * The order of the elements is preserved.
     *
     * @param Closure $p The predicate used for filtering.
     *
     * @return Collection A collection with the results of the filter operation.
     */
    function filter(Closure $p)
    {
        $this->initialize();
        return $this->collection->filter($p);
    }

    /**
     * Tests whether the given predicate p holds for all elements of this collection.
     *
     * @param Closure $p The predicate.
     *
     * @return boolean TRUE, if the predicate yields TRUE for all elements, FALSE otherwise.
     */
    function forAll(Closure $p)
    {
        $this->initialize();
        return $this->collection->forAll($p);
    }

    /**
     * Applies the given function to each element in the collection and returns
     * a new collection with the elements returned by the function.
     *
     * @param Closure $func
     *
     * @return Collection
     */
    function map(Closure $func)
    {
        $this->initialize();
        return $this->collection->map($func);
    }

    /**
     * Partitions this collection in two collections according to a predicate.
     * Keys are preserved in the resulting collections.
     *
     * @param Closure $p The predicate on which to partition.
     *
     * @return array An array with two elements. The first element contains the collection
     *               of elements where the predicate returned TRUE, the second element
     *               contains the collection of elements where the predicate returned FALSE.
     */
    function partition(Closure $p)
    {
        $this->initialize();
        return $this->collection->partition($p);
    }

    /**
     * Gets the index/key of a given element. The comparison of two elements is strict,
     * that means not only the value but also the type must match.
     * For objects this means reference equality.
     *
     * @param mixed $element The element to search for.
     *
     * @return int|string|bool The key/index of the element or FALSE if the element was not found.
     */
    function indexOf($element)
    {
        $this->initialize();
        return $this->collection->indexOf($element);
    }

    /**
     * Extracts a slice of $length elements starting at position $offset from the Collection.
     *
     * If $length is null it returns all elements from $offset to the end of the Collection.
     * Keys have to be preserved by this method. Calling this method will only return the
     * selected slice and NOT change the elements contained in the collection slice is called on.
     *
     * @param int $offset The offset to start from.
     * @param int|null $length The maximum number of elements to return, or null for no limit.
     *
     * @return array
     */
    function slice($offset, $length = NULL)
    {
        $this->initialize();
        return $this->collection->slice($offset, $length);
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
        $this->initialize();
        return new InnerTermIterator($this->collection->getIterator());
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        $this->initialize();
        return $this->collection->offsetExists($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        $this->initialize();
        return $this->collection->offsetGet($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->initialize();
        $this->collection->offsetSet($offset, $value);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->initialize();
        $this->collection->offsetUnset($offset);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        $this->initialize();
        return $this->collection->count();
    }

    /**
     * Replace all contents of the collection.
     *
     * @param array $values
     */
    public function replace(array $values)
    {
        $this->clear();
        foreach ($values as $value) {
            $this->add($value);
        }
    }

    /**
     * @return bool
     */
    public function isInitialized()
    {
        return (boolean) $this->initialized;
    }

    /**
     * @return boolean
     */
    public function isDirty()
    {
        $this->initialize();
        return (boolean) $this->dirty;
    }

    /**
     * @param boolean $dirty
     * @return void
     */
    public function setDirty($dirty)
    {
        $this->dirty = $dirty;
    }
}