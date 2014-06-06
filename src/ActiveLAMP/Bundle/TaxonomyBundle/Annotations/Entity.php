<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 3:38 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Annotations;


/**
 * Class Entity
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Annotations
 * @author Bez Hermoso <bez@activelamp.com>
 * @Annotation
 */
class Entity 
{
    public $type = null;

    public $identifier = 'id';

    public function getType()
    {
        return $this->type;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }
}