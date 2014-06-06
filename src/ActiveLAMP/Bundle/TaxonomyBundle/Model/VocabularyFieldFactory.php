<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/4/14
 * Time: 10:00 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Model;

use ActiveLAMP\Bundle\TaxonomyBundle\Entity\PluralVocabularyField;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\SingularVocabularyField;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\VocabularyFieldInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;


/**
 * Class VocabularyFieldFactory
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Model
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
     * @param \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary $vocabulary
     * @param $type
     * @param $identifier
     * @param null $previousValue
     * @param bool $singular
     * @return PluralVocabularyField|\ActiveLAMP\Bundle\TaxonomyBundle\Entity\SingularVocabularyField|VocabularyFieldInterface
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