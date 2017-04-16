<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/3/14
 * Time: 12:33 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Validation\Constraint;
use Symfony\Component\Validator\Constraint;


/**
 * Class VocabularyField
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Validation\Constraint
 * @author Bez Hermoso <bez@activelamp.com>
 */
class VocabularyField extends Constraint
{
    public $singular = false;
    public $vocabulary;
    public $required = false;

    public $invalidTypeMessage = 'Invalid type provided.';
    public $invalidPluralTypeMessage = 'Invalid term collection value.';
    public $invalidTermMessage = 'Term does not belong in vocabulary';
    public $requiredMessage = 'Cannot be empty.';

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