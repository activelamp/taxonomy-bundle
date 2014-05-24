<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 6:11 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Entity;
use ActiveLAMP\TaxonomyBundle\Iterator\InnerTermIterator;
use ActiveLAMP\TaxonomyBundle\Metadata\Entity;
use Doctrine\ORM\EntityManager;
use Traversable;


/**
 * Class TermsLazyLoadCollection
 *
 * @package ActiveLAMP\TaxonomyBundle\Entity
 * @author Bez Hermoso <bez@activelamp.com>
 */
class TermsLazyLoadCollection implements \IteratorAggregate
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Entity
     */
    protected $entityMetadata;

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var mixed
     */
    protected $identifier;

    protected $entityTerms;

    protected $new = array();

    protected $toRemove = array();

    protected $entity;

    protected $snapshot;

    public function __construct(EntityManager $em, Entity $entityMetadata, $identifier)
    {
        $this->em = $em;
        $this->entityMetadata = $entityMetadata;
        $this->identifier = $identifier;
    }

    /**
     *
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable|Term[] An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        if ($this->loaded == false) {
            $this->load();
        }
        return new InnerTermIterator(new \ArrayIterator($this->entityTerms));
    }

    protected function load()
    {
        if ($this->loaded) {
            return;
        }

        $type = $this->entityMetadata->getType();

        $items =
            $this->em->getRepository('ALTaxonomyBundle:EntityTerm')
                ->findBy(array(
                    'entityType' => $type,
                    'entityIdentifier' => $this->identifier
                ));

        $this->snapshot = $items;

        $this->entityTerms = $items;

        $this->loaded = true;
    }

    public function removeTerm(Term $term)
    {
        $this->load();

        $haystack = iterator_to_array($this->getIterator());

        $i = array_search($term, $haystack, true);

        if ($i !== false) {
            unset($this->entityTerms[$i]);
        }
    }

    public function addTerm(Term $term)
    {
        $this->load();

        $haystack = iterator_to_array($this->getIterator());

        $i = array_search($term, $haystack, true);

        if ($i === false) {
            $e = new EntityTerm();
            $e->setTerm($term);
            $this->entityTerms[] = $e;
        }
    }

    public function getInsertDiff()
    {
        return array_udiff_assoc(
            $this->entityTerms,
            $this->snapshot,
            function ($x, $y) {
                return $x === $y ? 0 : 1;
            }
        );
    }

    public function getDeleteDiff()
    {
        return array_udiff_assoc(
            $this->snapshot,
            $this->entityTerms,
            function ($x, $y) {
                return $x === $y ? 0 : 1;
            }
        );
    }
}