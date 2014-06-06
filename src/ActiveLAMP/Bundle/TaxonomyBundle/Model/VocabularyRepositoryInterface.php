<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/4/14
 * Time: 9:15 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Model;

use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary;


/**
 * Class VocabularyRepositoryInterface
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Model
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