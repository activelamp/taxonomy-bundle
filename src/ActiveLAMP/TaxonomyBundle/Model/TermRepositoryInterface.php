<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/4/14
 * Time: 9:18 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Model;
use ActiveLAMP\TaxonomyBundle\Entity\Term;


/**
 * Class TermRepositoryInterface
 *
 * @package ActiveLAMP\TaxonomyBundle\Model
 * @author Bez Hermoso <bez@activelamp.com>
 */
interface TermRepositoryInterface
{
    /**
     * @param $id
     * @return Term
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
     * @return Term[]
     */
    public function findAll();
}