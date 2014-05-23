<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 4:33 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Doctrine\EventListener;
use ActiveLAMP\TaxonomyBundle\Entity\Vocabulary;
use ActiveLAMP\TaxonomyBundle\Entity\VocabularyField;
use ActiveLAMP\TaxonomyBundle\Metadata\TaxonomyMetadata;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;


/**
 * Class LoadVocabularyFields
 *
 * @package ActiveLAMP\TaxonomyBundle\Doctrine\EventListener
 * @author Bez Hermoso <bez@activelamp.com>
 */
class LoadVocabularyFields implements EventSubscriber
{

    /**
     * @var TaxonomyMetadata
     */
    protected $metadata;

    /**
     * @param TaxonomyMetadata $metadata
     */
    public function __construct(TaxonomyMetadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::postLoad
        );
    }

    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        $metadata = $this->metadata->getEntityMetadata($entity);

        if (!$metadata) {
            return;
        }

        foreach ($metadata->getVocabularies() as $vocabularyMetdata) {

            $reflectionProperty = $vocabularyMetdata->getReflectionProperty();
            /** @var $vocabulary Vocabulary */
            $vocabulary = $eventArgs->getEntityManager()
                                    ->getRepository('ALTaxonomyBundle:Vocabulary')
                                    ->findOneBy(array('name' => $vocabularyMetdata->getName()));

            if (!$vocabulary) {
                throw new \RuntimeException(
                    sprintf(
                        'Cannot find "%s" vocabulary. Cannot link to %s::%s',
                        $vocabularyMetdata->getName(),
                        $metadata->getReflectionClass()->getName(),
                        $reflectionProperty->getName()
                    ));
            }

            $vocabularyField = new VocabularyField($vocabulary);

            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($entity, $vocabularyField);
            $reflectionProperty->setAccessible(false);

        }
    }
}