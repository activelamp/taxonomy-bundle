<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/4/14
 * Time: 9:39 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Model;

use ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;


/**
 * Class EntityTermsFinder
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Model
 * @author Bez Hermoso <bez@activelamp.com>
 */
class EntityTermsFinder
{
    protected $vocabulary;

    protected $type;

    protected $identifier;

    protected $em;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param Vocabulary $vocabulary
     * @param $type
     * @param $identifier
     */
    public function __construct(ObjectManager $manager, Vocabulary $vocabulary, $type, $identifier)
    {
        $this->em = $manager;
        $this->vocabulary = $vocabulary;
        $this->type = $type;
        $this->identifier = $identifier;
    }

    /**
     * @return array|EntityTerm[]
     */
    public function find()
    {
        $eTerms = $this->em
            ->getRepository('ALTaxonomyBundle:EntityTerm')
            ->createQueryBuilder('et')
            ->innerJoin('et.term', 't')
            ->innerJoin('t.vocabulary', 'v')
            ->addSelect('t')
            ->andWhere('v.id = :vid')
            ->andWhere('et.entityType = :type')
            ->andWhere('et.entityIdentifier = :id')
            ->setParameters(array(
                'vid' => $this->vocabulary->getId(),
                'id' => $this->identifier,
                'type' => $this->type,
            ))->getQuery()->getResult();

        return $eTerms;
    }

    /**
     * @return mixed
     */
    public function findOne()
    {
        $eTerm = $this->em
            ->getRepository('ALTaxonomyBundle:EntityTerm')
            ->createQueryBuilder('et')
            ->innerJoin('et.term', 't')
            ->innerJoin('t.vocabulary', 'v')
            ->addSelect('t')
            ->andWhere('v.id = :vid')
            ->andWhere('et.entityType = :type')
            ->andWhere('et.entityIdentifier = :id')
            ->setParameters(array(
                'vid' => $this->vocabulary->getId(),
                'id' => $this->identifier,
                'type' => $this->type,
            ))
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        return $eTerm;
    }
}