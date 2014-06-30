<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/30/14
 * Time: 9:12 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Entity\Repository;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary;
use ActiveLAMP\Bundle\TaxonomyBundle\Model\EntityTermRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;


/**
 * Class EntityTermRepository
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Entity\Repository
 * @author Bez Hermoso <bez@activelamp.com>
 */
class EntityTermRepository extends EntityRepository implements EntityTermRepositoryInterface
{

    /**
     * @param string|Vocabulary $vocabulary
     * @param string $entityType
     * @param string $entityIdentifier
     * @return EntityTerm[]
     */
    public function findEntities($vocabulary, $entityType, $entityIdentifier)
    {
        $eTerms =
            $this
            ->createQueryBuilder('et')
            ->innerJoin('et.term', 't')
            ->innerJoin('t.vocabulary', 'v')
            ->addSelect('t')
            ->andWhere('v.id = :vid')
            ->andWhere('et.entityType = :type')
            ->andWhere('et.entityIdentifier = :id')
            ->setParameters(array(
                'vid' => $vocabulary,
                'id' => $entityIdentifier,
                'type' => $entityType,
            ))->getQuery()->getResult();

        return $eTerms;
    }

    /**
     * @param string|Vocabulary $vocabulary
     * @param string $entityType
     * @param string $entityIdentifier
     * @return EntityTerm
     */
    public function findEntity($vocabulary, $entityType, $entityIdentifier)
    {
        $eTerm =
            $this
            ->createQueryBuilder('et')
            ->innerJoin('et.term', 't')
            ->innerJoin('t.vocabulary', 'v')
            ->addSelect('t')
            ->andWhere('v.id = :vid')
            ->andWhere('et.entityType = :type')
            ->andWhere('et.entityIdentifier = :id')
            ->setParameters(array(
                'vid' => $vocabulary,
                'id' => $entityIdentifier,
                'type' => $entityType,
            ))
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        return $eTerm;
    }
}