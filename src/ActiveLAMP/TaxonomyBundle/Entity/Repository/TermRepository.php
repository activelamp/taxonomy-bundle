<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/4/14
 * Time: 10:31 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Entity\Repository;
use ActiveLAMP\TaxonomyBundle\Entity\Term;
use ActiveLAMP\TaxonomyBundle\Model\TermRepositoryInterface;
use Doctrine\ORM\EntityRepository;


/**
 * Class TermRepository
 *
 * @package ActiveLAMP\TaxonomyBundle\Entity\Repository
 * @author Bez Hermoso <bez@activelamp.com>
 */
class TermRepository extends EntityRepository implements TermRepositoryInterface
{

    /**
     * @param $id
     * @return Term
     */
    public function findById($id)
    {
        return $this->find($id);
    }

    /**
     * @param $name
     * @return Term
     */
    public function findByName($name)
    {
        return $this->findOneBy(array(
            'name' => $name,
        ));
    }

    /**
     * @param $vocabulary
     * @return Term[]
     */
    public function findByVocabulary($vocabulary)
    {
        return $this->findBy(array(
            'vocabulary' => $vocabulary,
        ), array('weight' => 'desc'));
    }
}