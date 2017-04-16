<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/21/14
 * Time: 5:19 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy;

use ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\EventListener\LoadVocabularyFields;
use ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\EventListener\PrepareEntityTerms;
use ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\EventListener\RelatedEntities;
use ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\EventListener\RemoveEntityTerms;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\EntityTerm;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;


/**
 * Class TaxonomyService
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Model
 * @author Bez Hermoso <bez@activelamp.com>
 */
class TaxonomyService extends AbstractTaxonomyService
{
    public function __construct(ObjectManager $manager)
    {
        /** @var $manager EntityManager */
        if (!$manager instanceof EntityManager) {
            throw new \InvalidArgumentException(sprintf('%s requires Doctrine\\ORM\\EntityManager.', __CLASS__));
        }

        parent::__construct($manager);

        $eventManager = $manager->getEventManager();
        $eventManager->addEventSubscriber(new LoadVocabularyFields($this));
        $eventManager->addEventSubscriber(new PrepareEntityTerms($this));
        $eventManager->addEventSubscriber(new RelatedEntities($this));
        $eventManager->addEventSubscriber(new RemoveEntityTerms($this));
    }

    /**
     * @param EntityTerm $entityTerm
     * @param bool $flush
     */
    public function saveEntityTerm(EntityTerm $entityTerm, $flush = true)
    {
        //Injection of entity-type and entity-identifier is done with PrepareEntityTerms subscriber.
        $this->em->persist($entityTerm);

        if ($flush === true) {
            $this->em->flush();
        }
    }


}