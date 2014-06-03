<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/3/14
 * Time: 12:33 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Validation\Constraint;
use Symfony\Component\Validator\Constraint;


/**
 * Class VocabularyField
 *
 * @package ActiveLAMP\TaxonomyBundle\Validation\Constraint
 * @author Bez Hermoso <bez@activelamp.com>
 */
class VocabularyField extends Constraint
{
    public $singular = false;
    public $vocabulary;
    public $invalidTypeMessage = 'Invalid type provided.';
    public $invalidTermMessage = 'Term does not belong in vocabulary';

    public function getRequiredOptions()
    {
        return array(
            'vocabulary'
        );
    }

    public function validatedBy()
    {
        return 'vocabulary_field_validator';
    }


} 