<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/3/14
 * Time: 12:34 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Validation\Constraint;
use ActiveLAMP\TaxonomyBundle\Entity\Term;
use ActiveLAMP\TaxonomyBundle\Model\TaxonomyService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


/**
 * Class VocabularyFieldValidator
 *
 * @package ActiveLAMP\TaxonomyBundle\Validation\Constraint
 * @author Bez Hermoso <bez@activelamp.com>
 */
class VocabularyFieldValidator extends ConstraintValidator
{
    protected $service;

    public function __construct(TaxonomyService $service)
    {
        $this->service = $service;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint|VocabularyField $constraint The constraint for the validation
     *
     * @throws \LogicException
     * @api
     */
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint VocabularyField */

        if (!$value instanceof Term) {
            $this->context->addViolation($constraint->invalidTypeMessage, array(), $value);
        }

        $vocabulary = $this->service->findVocabularyByName($constraint->vocabulary);

        if (!$vocabulary) {
            throw new \LogicException(sprintf("Cannot find vocabulary of name %s", $constraint->vocabulary));
        }
        try {
            $vocabulary->getTermByName($value->getName());
        } catch (\Exception $e) {
            $this->context->addViolation($constraint->invalidTermMessage, array(), $value->getName());
        }
    }
}