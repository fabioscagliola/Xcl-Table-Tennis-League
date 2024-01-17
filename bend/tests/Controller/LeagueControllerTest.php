<?php

namespace App\Tests\Controller;

use App\DataTransferObject\LeagueData;
use App\Entity\League;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class LeagueControllerTest extends ControllerTest
{
    private static string $routeUrl = '/leagues';

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenEmptyName_WhenCreatingLeague_ThenBadRequest(): void
    {
        $data = new LeagueData('');
        static::performRequest(static::$routeUrl, 'POST', $data, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function GivenValidRequest_WhenCreatingLeague_ThenOk(): void
    {
        $data = static::CreateLeagueData();
        $expected = new League();
        $expected->initFromData(static::$entityManager, $data);
        $actual = static::CreateLeague($data);
        static::assertGreaterThan(0, $actual->getId());
        static::makeAssertions($expected, $actual);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenInvalidIdentifier_WhenReadingLeague_ThenNotFound(): void
    {
        static::performRequest(static::$routeUrl . '/0', 'GET', null, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function GivenValidIdentifier_WhenReadingLeague_ThenOk(): void
    {
        $data = static::CreateLeagueData();
        $expected = static::CreateLeague($data);
        $response = static::performRequest(
            static::$routeUrl . '/' . $expected->getId(),
            'GET',
            null,
            Response::HTTP_OK
        );
        $actual = static::$serializer->deserialize($response->getContent(), League::class, 'json');
        static::assertEquals($expected->getId(), $actual->getId());
        static::makeAssertions($expected, $actual);
    }

    /**
     * @test
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function GivenExistingLeagues_WhenReadingLeagueList_ThenOk(): void
    {
        $data = static::CreateLeagueData();
        $expected = static::CreateLeague($data);
        $response = static::performRequest(static::$routeUrl, 'GET', null, Response::HTTP_OK);
        $actualList = static::$serializer->deserialize($response->getContent(), League::class . '[]', 'json');
        static::assertGreaterThan(0, count($actualList));
        $actual = $actualList[count($actualList) - 1];
        static::assertEquals($expected->getId(), $actual->getId());
        static::makeAssertions($expected, $actual);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenNonExistingLeague_WhenUpdatingLeague_ThenNotFound(): void
    {
        $data = static::CreateLeagueData();
        static::performRequest(static::$routeUrl . '/0', 'PUT', $data, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function GivenExistingLeague_WhenUpdatingLeague_ThenOk(): void
    {
        $data = static::CreateLeagueData();
        $existing = static::CreateLeague($data);
        $data = new LeagueData('Minor');
        $expected = new League();
        $expected->initFromData(static::$entityManager, $data);
        $response = static::performRequest(
            static::$routeUrl . '/' . $existing->getId(),
            'PUT',
            $data,
            Response::HTTP_OK
        );
        $actual = static::$serializer->deserialize($response->getContent(), League::class, 'json');
        static::assertEquals($existing->getId(), $actual->getId());
        static::makeAssertions($expected, $actual);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenNonExistingLeague_WhenDeletingLeague_ThenNotFound(): void
    {
        static::performRequest(static::$routeUrl . '/0', 'DELETE', null, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function GivenExistingLeague_WhenDeletingLeague_ThenOk(): void
    {
        $data = static::CreateLeagueData();
        $existing = static::CreateLeague($data);
        static::performRequest(static::$routeUrl . '/' . $existing->getId(), 'DELETE', null, Response::HTTP_NO_CONTENT);
        static::performRequest(static::$routeUrl . '/' . $existing->getId(), 'GET', null, Response::HTTP_NOT_FOUND);
    }

    // TODO: GivenNonExistingLeague_WhenAssigningPlayer_ThenBadRequest
    // TODO: GivenNonExistingPlayer_WhenAssigningPlayer_ThenBadRequest

    /**
     * @test
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function GivenExistingLeague_WhenAssigningPlayer_ThenOk(): void
    {
        $playerData = static::CreatePlayerData();
        $player = static::CreatePlayer($playerData);
        $data = static::CreateLeagueData();
        $expected = static::CreateLeague($data);
        static::performRequest(
            static::$routeUrl . '/' . $expected->getId() . '/players/' . $player->getId(),
            'PUT',
            null,
            Response::HTTP_NO_CONTENT
        );
        $response = static::performRequest(
            static::$routeUrl . '/' . $expected->getId(),
            'GET',
            null,
            Response::HTTP_OK
        );
        $actual = static::$serializer->deserialize($response->getContent(), League::class, 'json');
        static::assertEquals($expected->getId(), $actual->getId());
        static::assertGreaterThan(0, count($actual->getPlayerList()));
        static::assertEquals($player->getId(), $actual->getPlayers()[count($actual->getPlayerList()) - 1]->getId());
    }

    private static function makeAssertions(League $expected, League $actual): void
    {
        static::assertNotNull($expected);
        static::assertNotNull($actual);
        static::assertEquals($expected->getName(), $actual->getName());
    }
}