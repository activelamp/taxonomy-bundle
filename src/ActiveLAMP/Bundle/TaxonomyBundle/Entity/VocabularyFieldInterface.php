<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/28/14
 * Time: 3:36 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Entity;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary;


/**
 * Interface VocabularyFieldInterface
 * 
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Entity
 * @author Bez Hermoso <bez@activelamp.com>
 */
interface VocabularyFieldInterface 
{
    /**
     * @return array|EntityTerm[]
     */
    public function getInsertDiff();

    /**
     * @return array|EntityTerm[]
     */
    public function getDeleteDiff();

    /**
     * @return Vocabulary
     */
    public function getVocabulary();
}