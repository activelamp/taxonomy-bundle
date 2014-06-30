<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/3/14
 * Time: 12:34 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Validation\Constraint;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\PluralVocabularyField;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\SingularVocabularyField;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\VocabularyFieldInterface;
use ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\TaxonomyService;
use ActiveLAMP\Bundle\TaxonomyBundle\Validation\Constraint\VocabularyField;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


/**
 * Class VocabularyFieldValidator
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Validation\Constraint
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
        if (!$value) {
            return;
        }

        if ($constraint->singular === true) {
            $this->validateSingular($value, $constraint);
        } else {
            $this->validatePlural($value, $constraint);
        }
    }

    protected function validateSingular($value, VocabularyField $constraint)
    {
        /* @var $constraint VocabularyField */

        if ($value instanceof SingularVocabularyField) {
            $value = $value->getTerm();
        }

        if ($constraint->required === true && !$value) {
            $this->context->addViolation($constraint->requiredMessage);
            return;
        } elseif (!$value) {
            return;
        }

        if (!$value instanceof Term) {
            $this->context->addViolation($constraint->invalidTypeMessage);
        }

        $vocabulary = $this->service->findVocabularyByName($constraint->vocabulary);
        if (!$vocabulary) {

            throw new \LogicException(sprintf("Cannot find vocabulary of name %s", $constraint->vocabulary));
        }

        if ($value->getVocabulary()->getName() !== $vocabulary->getName()) {
            $this->context->addViolation($constraint->invalidTermMessage);
        }
    }

    protected function validatePlural($value, VocabularyField $constraint)
    {
        if ($value instanceof PluralVocabularyField) {
            $value = $value->getTerms();
        }

        if ($constraint->required === true && (!$value OR count($value) == 0)) {
            $this->context->addViolation($constraint->requiredMessage);
            return;
        } elseif (!$value) {
            return;
        }

        if (!is_array($value) && $value instanceof \Traversable) {
            $this->context->addViolation($constraint->invalidPluralTypeMessage);
        }

        $vocabulary = $this->service->findVocabularyByName($constraint->vocabulary);

        if (!$vocabulary) {
            throw new \LogicException(sprintf("Cannot find vocabulary of name %s", $constraint->vocabulary));
        }

        foreach ($value as $term) {

            if (!$term instanceof Term) {
                $this->context->addViolation($constraint->invalidTypeMessage);
                continue;
            }

            if ($term->getVocabulary()->getName() !== $vocabulary->getName()) {
                $this->context->addViolation($constraint->invalidTermMessage);
            }
        }


    }
}