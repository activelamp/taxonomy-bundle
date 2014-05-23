<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 4:29 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Entity;


/**
 * Class VocabularyField
 *
 * @package ActiveLAMP\TaxonomyBundle\Entity
 * @author Bez Hermoso <bez@activelamp.com>
 */
class VocabularyField 
{
    protected $terms = array();

    protected $vocabulary;

    public function __construct(Vocabulary $vocabulary)
    {
        $this->vocabulary = $vocabulary;
    }

    public function getTerms()
    {
        return $this->terms;
    }

    public function getVocabulary()
    {
        return $this->vocabulary;
    }
}