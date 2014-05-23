<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 6:11 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Entity;
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

    /**
     * @var array|Term[]
     */
    protected $terms = array();

    public function __construct(EntityManager $em, Entity $entityMetadata, $identifier)
    {
        $this->em = $em;
        $this->entityMetadata = $entityMetadata;
        $this->identifier = $identifier;
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
        if ($this->loaded == false) {
            $this->load();
        }

        return new \ArrayIterator($this->terms);
    }

    protected function load()
    {

        $type = $this->entityMetadata->getType();

        $items =
            $this->em->getRepository('ALTaxonomyBundle:EntityTerm')
                ->findBy(array(
                    'entityType' => $type,
                    'entityIdentifier' => $this->identifier
                ));

        $this->terms = new RelatedTermCollection($items);

        $this->loaded = true;
    }
}