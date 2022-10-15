<?php

namespace Atournayre\Bundle\HistoriqueBundle\Service;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class Serializer
{
    private \Symfony\Component\Serializer\Serializer $serializer;

    public function __construct()
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $this->serializer = new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);
    }

    /**
     * @param array $object
     *
     * @return bool|float|int|string
     */
    public function serialize(array $object): float|bool|int|string
    {
        return $this->serializer->serialize($object, 'json');
    }

    /**
     * @param string $serializedObject
     *
     * @return object
     * @throws ExceptionInterface
     */
    public function deserialize(string $serializedObject): object
    {
        return $this->serializer->denormalize($serializedObject, 'json');
    }
}
