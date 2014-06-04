<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/4/14
 * Time: 10:00 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Model;
use ActiveLAMP\TaxonomyBundle\Entity\PluralVocabularyField;
use ActiveLAMP\TaxonomyBundle\Entity\SingularVocabularyField;
use ActiveLAMP\TaxonomyBundle\Entity\Term;
use ActiveLAMP\TaxonomyBundle\Entity\Vocabulary;
use ActiveLAMP\TaxonomyBundle\Entity\VocabularyFieldInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;


/**
 * Class VocabularyFieldFactory
 *
 * @package ActiveLAMP\TaxonomyBundle\Model
 * @author Bez Hermoso <bez@activelamp.com>
 */
class VocabularyFieldFactory 
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Vocabulary $vocabulary
     * @param $type
     * @param $identifier
     * @param null $previousValue
     * @param bool $singular
     * @return PluralVocabularyField|SingularVocabularyField|VocabularyFieldInterface
     */
    public function createVocabularyField(Vocabulary $vocabulary, $type, $identifier, $previousValue = null, $singular = false)
    {
        if ($singular === true) {
            return new SingularVocabularyField(
                $this->em,
                $vocabulary,
                $type,
                $identifier,
                $previousValue instanceof Term ? $previousValue : null
            );
        } else {
            return new PluralVocabularyField(
                $this->em,
                $vocabulary,
                $type,
                $identifier,
                $previousValue instanceof Collection ? $previousValue : null
            );
        }
    }
} 