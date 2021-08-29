<?php

namespace Atournayre\Bundle\HistoriqueBundle\Service;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class Serializer
{
    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    private $serializer;

    public function __construct()
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $this->serializer = new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);
    }

    /**
     * @param object $object
     *
     * @return bool|float|int|string
     */
    public function serialize($object)
    {
        return $this->serializer->serialize($object, 'json');
    }

    /**
     * @param string $serializedObject
     *
     * @return object
     */
    public function deserialize($serializedObject)
    {
        return $this->serializer->denormalize($serializedObject, 'json');
    }
}
