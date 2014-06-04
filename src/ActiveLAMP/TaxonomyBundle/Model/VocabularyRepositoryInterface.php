<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/4/14
 * Time: 9:15 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Model;


/**
 * Class VocabularyRepositoryInterface
 *
 * @package ActiveLAMP\TaxonomyBundle\Model
 * @author Bez Hermoso <bez@activelamp.com>
 */
interface VocabularyRepositoryInterface
{
    public function findById($id);

    public function findByName($name);

    public function findAll();
}