<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/30/14
 * Time: 10:54 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Form\DataTransformer;

use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term;
use ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\TaxonomyService;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


/**
 * Class SingularVocabularyFieldTransformer
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Form\DataTransformer
 * @author Bez Hermoso <bez@activelamp.com>
 */
class SingularVocabularyFieldTransformer implements DataTransformerInterface
{

    protected $service;

    protected $vocabulary;

    /**
     * @param \ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\TaxonomyService $service
     * @param $vocabulary
     */
    public function __construct(TaxonomyService $service, $vocabulary)
    {
        $this->service = $service;
        $this->vocabulary = $vocabulary;
    }

    /**
     * @param mixed $value
     * @return mixed|string
     */
    public function transform($value)
    {
        if (null === $value) {
            return "";
        }

        return $value->getName();
    }

    /**
     * @param mixed $value
     * @return Term|mixed|null
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform($value)
    {

        if (!$value) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException(sprintf('Expected string. "%s" given.', gettype($value)));
        }

        $term = $this->service->findTermByName($value);

        if (!$term) {
            throw new TransformationFailedException('Cannot find term.');
        }


        if (is_string($this->vocabulary)
        && $term->getVocabulary()->getName() !== $this->vocabulary) {
            throw new TransformationFailedException(sprintf(
                'Term "%s" does not belong in vocabulary "%s"',
                $term->getName(),
                $this->vocabulary));
        }

        return $term;
    }
}