<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/4/14
 * Time: 9:15 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Model;
use ActiveLAMP\TaxonomyBundle\Entity\Vocabulary;


/**
 * Class VocabularyRepositoryInterface
 *
 * @package ActiveLAMP\TaxonomyBundle\Model
 * @author Bez Hermoso <bez@activelamp.com>
 */
interface VocabularyRepositoryInterface
{
    /**
     * @param $id
     * @return Vocabulary
     */
    public function findById($id);

    /**
     * @param $name
     * @return Vocabulary
     */
    public function findByName($name);

    /**
     * @return Vocabulary[]
     */
    public function findAll();
}