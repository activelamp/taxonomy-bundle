<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/4/14
 * Time: 9:18 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Model;

use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term;


/**
 * Class TermRepositoryInterface
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Model
 * @author Bez Hermoso <bez@activelamp.com>
 */
interface TermRepositoryInterface
{
    /**
     * @param $id
     * @return \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term
     */
    public function findById($id);

    /**
     * @param $name
     * @return Term
     */
    public function findByName($name);

    /**
     * @param $vocabulary
     * @return Term[]
     */
    public function findByVocabulary($vocabulary);

    /**
     * @return \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term[]
     */
    public function findAll();
}