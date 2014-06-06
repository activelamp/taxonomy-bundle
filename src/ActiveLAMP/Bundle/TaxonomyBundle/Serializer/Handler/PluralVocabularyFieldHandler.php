<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/27/14
 * Time: 1:32 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Serializer\Handler;

use ActiveLAMP\Bundle\TaxonomyBundle\Entity\PluralVocabularyField;
use ActiveLAMP\Bundle\TaxonomyBundle\Serializer\SerializerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class PluralVocabularyFieldHandler
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Serializer\Handler
 * @author Bez Hermoso <bez@activelamp.com>
 */
class PluralVocabularyFieldHandler implements SubscribingHandlerInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    protected $serializeDefaults = array(
        'serializeMode' => array(
            'singular' => true,
            'value' => 'field'
        ),
    );

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->serializer = $container->get('al_taxonomy.serializer.array');
        $this->container = $container;
    }

    /**
     * Return format:
     *
     *      array(
     *          array(
     *              'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
     *              'format' => 'json',
     *              'type' => 'DateTime',
     *              'method' => 'serializeDateTimeToJson',
     *          ),
     *      )
     *
     * The direction and method keys can be omitted.
     *
     * @return array
     */
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'ActiveLAMP\\Bundle\\TaxonomyBundle\\Entity\\PluralVocabularyField',
                'method' => 'serializeVocabularyFieldToJson',
            ),
            array(
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'ActiveLAMP\\Bundle\\TaxonomyBundle\\Entity\\PluralVocabularyField',
                'method' => 'deserializeVocabularyFieldFromJson',
            ),
        );
    }

    public function serializeVocabularyFieldToJson(
        JsonSerializationVisitor $visitor,
        $field,
        array $type,
        Context $context
    ) {

        if (!$field instanceof PluralVocabularyField && $field instanceof \Traversable) {
            return iterator_to_array($field);
        }

        if ($field instanceof PluralVocabularyField) {
            $serialized = $this->serializer->serializeTerms($field->getTerms());
            return $serialized;
        }

        throw new \InvalidArgumentException("Cannot serialize. Expected PluralVocabularyField or Traversable containing Terms.");
    }

    public function deserializeVocabularyFieldFromJson(
        JsonDeserializationVisitor $visitor,
        $value,
        array $type,
        Context $context
    ) {
        return new ArrayCollection($this->serializer->deserializeTerms($value));
    }
}