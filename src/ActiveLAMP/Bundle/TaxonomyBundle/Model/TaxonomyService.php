<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 5:19 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Model;
use ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\QueryInjector;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\Bundle\TaxonomyBundle\Collection\RelatedEntityCollection;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\SingularVocabularyField;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\PluralVocabularyField;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\VocabularyFieldInterface;
use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\TaxonomyMetadata;
use ActiveLAMP\Bundle\TaxonomyBundle\Model\AbstractTaxonomyService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;


/**
 * Class TaxonomyService
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Model
 * @author Bez Hermoso <bez@activelamp.com>
 */
class TaxonomyService extends AbstractTaxonomyService
{

}