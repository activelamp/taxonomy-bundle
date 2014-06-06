<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/4/14
 * Time: 10:31 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Entity\Repository;

use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term;
use ActiveLAMP\Bundle\TaxonomyBundle\Model\TermRepositoryInterface;
use Doctrine\ORM\EntityRepository;


/**
 * Class TermRepository
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Entity\Repository
 * @author Bez Hermoso <bez@activelamp.com>
 */
class TermRepository extends EntityRepository implements TermRepositoryInterface
{

    /**
     * @param $id
     * @return \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term
     */
    public function findById($id)
    {
        return $this->find($id);
    }

    /**
     * @param $name
     * @return \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term
     */
    public function findByName($name)
    {
        return $this->findOneBy(array(
            'name' => $name,
        ));
    }

    /**
     * @param $vocabulary
     * @return \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term[]
     */
    public function findByVocabulary($vocabulary)
    {
        return $this->findBy(array(
            'vocabulary' => $vocabulary,
        ), array('weight' => 'desc'));
    }
}