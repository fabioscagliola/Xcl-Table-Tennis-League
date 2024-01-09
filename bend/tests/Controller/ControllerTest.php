<?php

namespace App\Tests\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\DataTransferObject\PlayerData;
use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class ControllerTest extends ApiTestCase
{
    protected static Client $client;
    protected static EntityManagerInterface $entityManager;
    protected static SerializerInterface $serializer;

    protected function setUp(): void
    {
        static::$client = static::createClient();
        static::$entityManager = static::getContainer()->get('doctrine')->getManager();
        static::$serializer = static::getContainer()->get(SerializerInterface::class);
    }

    /**
     * @param string $routeUrl
     * @param string $method
     * @param ?object $body
     * @param int $expectedStatusCode
     * @param string|null $bearer
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public static function performRequest(string $routeUrl, string $method, ?object $body, int $expectedStatusCode, string $bearer = null): ResponseInterface
    {
        $headerList = [];

        if ($bearer !== null)
            $headerList['Authorization'] = 'Bearer ' . $bearer;

        if ($method == 'POST' || $method == 'PUT') {
            if ($body == null) {
                throw new Exception('You must specify a body for POST and PUT requests!');
            } else {
                $headerList['Content-Type'] = 'application/json';
                $response = static::$client->request($method, $routeUrl, ['body' => static::$serializer->serialize($body, 'json'), 'headers' => $headerList]);
            }
        } else {
            $response = static::$client->request($method, $routeUrl, ['headers' => $headerList]);
        }

        static::assertResponseStatusCodeSame($expectedStatusCode);

        return $response;
    }

    static function CreatePlayerData(): PlayerData
    {
        return new PlayerData('Fabio');
    }

    static function CreatePlayer(PlayerData $playerData): Player
    {
        $response = static::performRequest('/players', 'POST', $playerData, Response::HTTP_CREATED);
        $response = static::performRequest('/players/' . $response->toArray()['id'], 'GET', null, Response::HTTP_OK);
        return static::$serializer->deserialize($response->getContent(), Player::class, 'json');
    }
}