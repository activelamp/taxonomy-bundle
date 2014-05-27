<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/27/14
 * Time: 1:32 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Serializer\Handler;
use ActiveLAMP\TaxonomyBundle\Entity\VocabularyField;
use ActiveLAMP\TaxonomyBundle\Metadata\TaxonomyMetadata;
use ActiveLAMP\TaxonomyBundle\Serializer\ArraySerializer;
use ActiveLAMP\TaxonomyBundle\Serializer\SerializerInterface;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;


/**
 * Class VocabularyFieldHandler
 *
 * @package ActiveLAMP\TaxonomyBundle\Serializer\Handler
 * @author Bez Hermoso <bez@activelamp.com>
 */
class VocabularyFieldHandler implements SubscribingHandlerInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(TaxonomyMetadata $metadata)
    {
        $this->serializer = new ArraySerializer($metadata);
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
                'type' => 'ActiveLAMP\\TaxonomyBundle\\Entity\\VocabularyField',
                'method' => 'serializeVocabularyFieldToJson',
            ),
        );
    }

    public function serializeVocabularyFieldToJson(
        JsonSerializationVisitor $visitor,
        VocabularyField $field,
        array $type,
        Context $context
    ) {
        $serialized = $this->serializer->serializeField($field);
        return $serialized;
    }
}