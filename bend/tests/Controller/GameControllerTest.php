<?php

namespace App\Tests\Controller;

use App\DataTransferObject\GameData;
use App\Entity\Game;
use DateTime;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GameControllerTest extends ControllerTest
{
    private static string $routeUrl = '/games';

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenEmptyLeagueId_WhenCreatingGame_ThenBadRequest(): void
    {
        $data = new GameData(
            0,
            (new DateTime())->format(DateTimeInterface::ATOM),
            1,
            'Fabio',
            '2-0'
        );
        static::performRequest(static::$routeUrl, 'POST', $data, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenEmptyDate_WhenCreatingGame_ThenBadRequest(): void
    {
        $data = new GameData(
            1,
            '',
            1,
            'Fabio',
            '2-0'
        );
        static::performRequest(static::$routeUrl, 'POST', $data, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenInvalidDate_WhenCreatingGame_ThenBadRequest(): void
    {
        $data = new GameData(
            1,
            (new DateTime())->format(DateTimeInterface::COOKIE),
            1,
            'Fabio',
            '2-0'
        );
        static::performRequest(static::$routeUrl, 'POST', $data, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenEmptyWinnerId_WhenCreatingGame_ThenBadRequest(): void
    {
        $data = new GameData(
            1,
            (new DateTime())->format(DateTimeInterface::ATOM),
            0,
            'Fabio',
            '2-0'
        );
        static::performRequest(static::$routeUrl, 'POST', $data, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenEmptyWinnerName_WhenCreatingGame_ThenBadRequest(): void
    {
        $data = new GameData(
            1,
            (new DateTime())->format(DateTimeInterface::ATOM),
            1,
            '',
            '2-0'
        );
        static::performRequest(static::$routeUrl, 'POST', $data, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenEmptyScore_WhenCreatingGame_ThenBadRequest(): void
    {
        $data = new GameData(
            1,
            (new DateTime())->format(DateTimeInterface::ATOM),
            1,
            'Fabio',
            ''
        );
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
    public function GivenValidRequest_WhenCreatingGame_ThenOk(): void
    {
        $data = static::CreateGameData();
        $expected = new Game();
        $expected->initFromData(static::$entityManager, $data);
        $actual = static::CreateGame($data);
        static::assertGreaterThan(0, $actual->getId());
        static::makeAssertions($expected, $actual);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenInvalidIdentifier_WhenReadingGame_ThenNotFound(): void
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
    public function GivenValidIdentifier_WhenReadingGame_ThenOk(): void
    {
        $data = static::CreateGameData();
        $expected = static::CreateGame($data);
        $response = static::performRequest(
            static::$routeUrl . '/' . $expected->getId(),
            'GET',
            null,
            Response::HTTP_OK
        );
        $actual = static::$serializer->deserialize($response->getContent(), Game::class, 'json');
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
    public function GivenExistingGames_WhenReadingGameList_ThenOk(): void
    {
        $data = static::CreateGameData();
        $expected = static::CreateGame($data);
        $response = static::performRequest(static::$routeUrl, 'GET', null, Response::HTTP_OK);
        $actualList = static::$serializer->deserialize($response->getContent(), Game::class . '[]', 'json');
        static::assertGreaterThan(0, count($actualList));
        $actual = $actualList[count($actualList) - 1];
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
    public function GivenNonExistingGame_WhenUpdatingGame_ThenNotFound(): void
    {
        $data = static::CreateGameData();
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
    public function GivenExistingGame_WhenUpdatingGame_ThenOk(): void
    {
        $data = static::CreateGameData();
        $existing = static::CreateGame($data);
        $data = new GameData(
            $existing->getLeague()->getId(),
            (new DateTime())->format(DateTimeInterface::ATOM),
            2,
            'Laura',
            '2-1'
        );
        $expected = new Game();
        $expected->initFromData(static::$entityManager, $data);
        $response = static::performRequest(
            static::$routeUrl . '/' . $existing->getId(),
            'PUT',
            $data,
            Response::HTTP_OK
        );
        $actual = static::$serializer->deserialize($response->getContent(), Game::class, 'json');
        static::assertEquals($existing->getId(), $actual->getId());
        static::makeAssertions($expected, $actual);
    }

    /**
     * @test
     * @throws TransportExceptionInterface
     */
    public function GivenNonExistingGame_WhenDeletingGame_ThenNotFound(): void
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
    public function GivenExistingGame_WhenDeletingGame_ThenOk(): void
    {
        $data = static::CreateGameData();
        $existing = static::CreateGame($data);
        static::performRequest(static::$routeUrl . '/' . $existing->getId(), 'DELETE', null, Response::HTTP_NO_CONTENT);
        static::performRequest(static::$routeUrl . '/' . $existing->getId(), 'GET', null, Response::HTTP_NOT_FOUND);
    }

    private static function makeAssertions(Game $expected, Game $actual): void
    {
        static::assertNotNull($expected);
        static::assertNotNull($actual);
        static::assertEquals($expected->getLeague()->getId(), $actual->getLeague()->getId());
        static::assertEquals($expected->getDate(), $actual->getDate());
        static::assertEquals($expected->getWinnerId(), $actual->getWinnerId());
        static::assertEquals($expected->getWinnerName(), $actual->getWinnerName());
        static::assertEquals($expected->getResult(), $actual->getResult());
    }
}