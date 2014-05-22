<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 2:15 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Annotations;
use Doctrine\Common\Annotations\Annotation;


/**
 * Class Vocabulary
 *
 * @package ActiveLAMP\TaxonomyBundle\Annotations
 * @author Bez Hermoso <bez@activelamp.com>
 *
 * @Annotation
 */
class Taxonomy extends Annotation
{
    protected $vocabularies = array();

    /**
     * @return array
     */
    public function getVocabularies()
    {
        return $this->vocabularies;
    }
}