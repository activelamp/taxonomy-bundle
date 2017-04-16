<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/30/14
 * Time: 9:09 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Model;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary;


/**
 * Interface EntityTermRepositoryInterface
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Model
 * @author Bez Hermoso <bez@activelamp.com>
 */
interface EntityTermRepositoryInterface
{
    /**
     * @param string|Vocabulary $vocabulary
     * @param string $entityType
     * @param string $entityIdentifier
     * @return EntityTerm[]
     */
    public function findEntities($vocabulary, $entityType, $entityIdentifier);

    /**
     * @param string|Vocabulary $vocabulary
     * @param string $entityType
     * @param string $entityIdentifier
     * @return EntityTerm
     */
    public function findEntity($vocabulary, $entityType, $entityIdentifier);
}