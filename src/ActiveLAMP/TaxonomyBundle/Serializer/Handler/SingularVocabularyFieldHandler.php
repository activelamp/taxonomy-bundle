<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/27/14
 * Time: 1:32 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Serializer\Handler;
use ActiveLAMP\TaxonomyBundle\Entity\SingularVocabularyField;
use ActiveLAMP\TaxonomyBundle\Serializer\ArraySerializer;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class MultipleVocabularyFieldHandler
 *
 * @package ActiveLAMP\TaxonomyBundle\Serializer\Handler
 * @author Bez Hermoso <bez@activelamp.com>
 */
class SingularVocabularyFieldHandler implements SubscribingHandlerInterface
{
    /**
     * @var ArraySerializer
     */
    protected $serializer;

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
                'type' => 'ActiveLAMP\\TaxonomyBundle\\Entity\\SingularVocabularyField',
                'method' => 'serializeVocabularyFieldToJson',
            ),
            array(
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'ActiveLAMP\\TaxonomyBundle\\Entity\\SingularVocabularyField',
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

        if (!$field instanceof SingularVocabularyField && $field instanceof \Traversable) {
            return $field;
        }

        if ($field instanceof SingularVocabularyField) {

            if ($field->getTerm()) {
                $serialized = $this->serializer->serializeTerm($field->getTerm());
                return $serialized;
            } else {
                return null;
            }
        }

        throw new \InvalidArgumentException("Cannot serialize. Expected SingularVocabularyField, array, or Traversable.");
    }

    public function deserializeVocabularyFieldFromJson(
        JsonDeserializationVisitor $visitor,
        $value,
        array $type,
        Context $context
    ) {
        if ($value !== null) {
            return $this->serializer->deserializeTerm($value);
        }
    }
}