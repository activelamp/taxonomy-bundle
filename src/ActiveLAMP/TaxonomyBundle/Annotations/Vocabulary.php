<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 2:45 PM
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
 *
 */
class Vocabulary extends Annotation
{
    protected $name = null;

    protected $columnName = null;

    public function getName()
    {
        return $this->name;
    }

    public function getColumnName()
    {
        if ($this->columnName === null) {
            $this->columnName = 'taxonomy_' . $this->name;
        }

        return $this->columnName;
    }
} 