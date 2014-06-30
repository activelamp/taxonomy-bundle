<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/10/14
 * Time: 1:19 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Form;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\SingularVocabularyField;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\VocabularyFieldInterface;
use ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\AbstractTaxonomyService;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;


/**
 * Class TermChoiceList
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Form
 * @author Bez Hermoso <bez@activelamp.com>
 */
class TermChoiceList implements ChoiceListInterface
{

    protected $initialized = false;

    protected $choices = array();

    protected $vocabulary;

    protected $taxonomy;

    /**
     * @var ObjectChoiceList
     */
    protected $objectChoiceList;

    public function __construct(AbstractTaxonomyService $taxonomy, $vocabulary)
    {
        $this->taxonomy = $taxonomy;
        $this->vocabulary = $vocabulary;
    }

    public function initialize()
    {
        if ($this->initialized === false) {
            $terms = $this->taxonomy->findTermsInVocabulary($this->vocabulary);
            $this->objectChoiceList = new ObjectChoiceList($terms, 'labelName', array(), null, 'name');
        }

        $this->initialized = true;
    }

    /**
     * Returns the list of choices
     *
     * @return array The choices with their indices as keys
     */
    public function getChoices()
    {
        $this->initialize();
        return $this->objectChoiceList->getChoices();
    }

    /**
     * Returns the values for the choices
     *
     * @return array The values with the corresponding choice indices as keys
     */
    public function getValues()
    {
        $this->initialize();
        return $this->objectChoiceList->getValues();
    }

    /**
     * Returns the choice views of the preferred choices as nested array with
     * the choice groups as top-level keys.
     *
     * Example:
     *
     * <source>
     * array(
     *     'Group 1' => array(
     *         10 => ChoiceView object,
     *         20 => ChoiceView object,
     *     ),
     *     'Group 2' => array(
     *         30 => ChoiceView object,
     *     ),
     * )
     * </source>
     *
     * @return array A nested array containing the views with the corresponding
     *               choice indices as keys on the lowest levels and the choice
     *               group names in the keys of the higher levels
     */
    public function getPreferredViews()
    {
        $this->initialize();
        return $this->objectChoiceList->getPreferredViews();
    }

    /**
     * Returns the choice views of the choices that are not preferred as nested
     * array with the choice groups as top-level keys.
     *
     * Example:
     *
     * <source>
     * array(
     *     'Group 1' => array(
     *         10 => ChoiceView object,
     *         20 => ChoiceView object,
     *     ),
     *     'Group 2' => array(
     *         30 => ChoiceView object,
     *     ),
     * )
     * </source>
     *
     * @return array A nested array containing the views with the corresponding
     *               choice indices as keys on the lowest levels and the choice
     *               group names in the keys of the higher levels
     *
     * @see getPreferredValues
     */
    public function getRemainingViews()
    {
        $this->initialize();
        return $this->objectChoiceList->getRemainingViews();
    }

    /**
     * Returns the choices corresponding to the given values.
     *
     * The choices can have any data type.
     *
     * The choices must be returned with the same keys and in the same order
     * as the corresponding values in the given array.
     *
     * @param array $values An array of choice values. Not existing values in
     *                      this array are ignored
     *
     * @return array An array of choices with ascending, 0-based numeric keys
     */
    public function getChoicesForValues(array $values)
    {
        $this->initialize();

        return $this->objectChoiceList->getChoicesForValues($values);

    }

    /**
     * Returns the values corresponding to the given choices.
     *
     * The values must be strings.
     *
     * The values must be returned with the same keys and in the same order
     * as the corresponding choices in the given array.
     *
     * @param array $choices An array of choices. Not existing choices in this
     *                       array are ignored
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     * @return array An array of choice values with ascending, 0-based numeric
     *               keys
     */
    public function getValuesForChoices(array $choices)
    {
        $this->initialize();

        $termChoices = array();

        foreach ($choices as $id => $choice) {
            if ($choice instanceof SingularVocabularyField) {
                $choice = $choice->getTerm();
            }
            $termChoices[$id] = $choice;
        }

        return $this->objectChoiceList->getValuesForChoices($termChoices);
    }

    /**
     * Returns the indices corresponding to the given choices.
     *
     * The indices must be positive integers or strings accepted by
     * {@link FormConfigBuilder::validateName()}.
     *
     * The index "placeholder" is internally reserved.
     *
     * The indices must be returned with the same keys and in the same order
     * as the corresponding choices in the given array.
     *
     * @param array $choices An array of choices. Not existing choices in this
     *                       array are ignored
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     * @return array An array of indices with ascending, 0-based numeric keys
     */
    public function getIndicesForChoices(array $choices)
    {
        $this->initialize();

        $termChoices = array();

        foreach ($choices as $id => $choice) {
            if ($choice instanceof SingularVocabularyField) {
                $choice = $choice->getTerm();
            }
            $termChoices[$id] = $choice;
        }

        return $this->objectChoiceList->getValuesForChoices($termChoices);
    }

    /**
     * Returns the indices corresponding to the given values.
     *
     * The indices must be positive integers or strings accepted by
     * {@link FormConfigBuilder::validateName()}.
     *
     * The index "placeholder" is internally reserved.
     *
     * The indices must be returned with the same keys and in the same order
     * as the corresponding values in the given array.
     *
     * @param array $values An array of choice values. Not existing values in
     *                      this array are ignored
     *
     * @return array An array of indices with ascending, 0-based numeric keys
     */
    public function getIndicesForValues(array $values)
    {
        $this->initialize();
        return $this->objectChoiceList->getIndicesForValues($values);
    }
}