<?php

namespace App\Tests\Controller;

use App\DataTransferObject\PlayerData;
use App\Entity\Player;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PlayerControllerTest extends ControllerTest
{
    static string $routeUrl = '/players';

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenEmptyName_WhenCreatingPlayer_ThenBadRequest(): void
    {
        $data = new PlayerData('');
        static::performRequest(static::$routeUrl, 'POST', $data, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GivenValidRequest_WhenCreatingPlayer_ThenOk(): void
    {
        $data = static::CreatePlayerData();
        $expected = new Player();
        $expected->initFromData(static::$entityManager, $data);
        $actual = static::CreatePlayer($data);
        static::assertGreaterThan(0, $actual->getId());
        static::makeAssertions($expected, $actual);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenInvalidIdentifier_WhenReadingPlayer_ThenNotFound(): void
    {
        static::performRequest(static::$routeUrl . '/0', 'GET', null, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function GivenValidIdentifier_WhenReadingPlayer_ThenOk(): void
    {
        $data = static::CreatePlayerData();
        $expected = static::CreatePlayer($data);
        $response = static::performRequest(static::$routeUrl . '/' . $expected->getId(), 'GET', null, Response::HTTP_OK);
        $actual = static::$serializer->deserialize($response->getContent(), Player::class, 'json');
        static::assertEquals($expected->getId(), $actual->getId());
        static::makeAssertions($expected, $actual);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function GivenExistingPlayers_WhenReadingPlayerList_ThenOk(): void
    {
        $data = static::CreatePlayerData();
        $expected = static::CreatePlayer($data);
        $response = static::performRequest(static::$routeUrl, 'GET', null, Response::HTTP_OK);
        $actualList = static::$serializer->deserialize($response->getContent(), Player::class . '[]', 'json');
        static::assertGreaterThan(0, count($actualList));
        $actual = $actualList[count($actualList) - 1];
        static::assertEquals($expected->getId(), $actual->getId());
        static::makeAssertions($expected, $actual);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenNonExistingPlayer_WhenUpdatingPlayer_ThenNotFound(): void
    {
        $data = static::CreatePlayerData();
        static::performRequest(static::$routeUrl . '/0', 'PUT', $data, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function GivenExistingPlayer_WhenUpdatingPlayer_ThenOk(): void
    {
        $data = static::CreatePlayerData();
        $existing = static::CreatePlayer($data);
        $data = new PlayerData('Laura');
        $expected = new Player();
        $expected->initFromData(static::$entityManager, $data);
        $response = static::performRequest(static::$routeUrl . '/' . $existing->getId(), 'PUT', $data, Response::HTTP_OK);
        $actual = static::$serializer->deserialize($response->getContent(), Player::class, 'json');
        static::assertEquals($existing->getId(), $actual->getId());
        static::makeAssertions($expected, $actual);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenNonExistingPlayer_WhenDeletingPlayer_ThenNotFound(): void
    {
        static::performRequest(static::$routeUrl . '/0', 'DELETE', null, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenExistingPlayer_WhenDeletingPlayer_ThenOk(): void
    {
        $data = static::CreatePlayerData();
        $existing = static::CreatePlayer($data);
        static::performRequest(static::$routeUrl . '/' . $existing->getId(), 'DELETE', null, Response::HTTP_NO_CONTENT);
        static::performRequest(static::$routeUrl . '/' . $existing->getId(), 'GET', null, Response::HTTP_NOT_FOUND);
    }

    static function makeAssertions(Player $expected, Player $actual): void
    {
        static::assertNotNull($expected);
        static::assertNotNull($actual);
        static::assertEquals($expected->getName(), $actual->getName());
    }
}