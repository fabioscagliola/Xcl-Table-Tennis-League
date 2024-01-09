<?php

namespace App\Controller;

use App\DataTransferObject\GameData;
use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    /**
     * Creates a game.
     *
     * @param EntityManagerInterface $entityManager
     * @param GameData $gameData The data of the game to be created.
     * @return JsonResponse
     */
    #[Route('/games', methods: ['POST'], format: 'json')]
    public function Create(EntityManagerInterface $entityManager,
        #[MapRequestPayload(acceptFormat: 'json', validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] GameData $gameData): JsonResponse
    {
        $game = new Game();
        try {
            $game->initFromData($entityManager, $gameData);
        } catch (Exception $e) {
            return static::json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        $entityManager->persist($game);
        $entityManager->flush();
        return static::json($game, Response::HTTP_CREATED);
    }

    /**
     * Retrieves a game.
     *
     * @param EntityManagerInterface $entityManager
     * @param int $id The identifier of the game.
     * @return JsonResponse
     */
    #[Route('/games/{id}', methods: ['GET'])]
    public function Read(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $repository = $entityManager->getRepository(Game::class);
        $game = $repository->find($id);
        if ($game == null)
            return static::json(null, Response::HTTP_NOT_FOUND);
        return static::json($game);
    }

    /**
     * Retrieves all the games.
     *
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/games', methods: ['GET'])]
    public function List(EntityManagerInterface $entityManager): JsonResponse
    {
        $repository = $entityManager->getRepository(Game::class);
        $gameList = $repository->findAll();
        return static::json($gameList, Response::HTTP_OK);
    }

    /**
     * Updates a game.
     *
     * @param EntityManagerInterface $entityManager
     * @param int $id The identifier of the game.
     * @param GameData $gameData The data of the game to be updated.
     * @return JsonResponse
     */
    #[Route('/games/{id}', methods: ['PUT'], format: 'json')]
    public function Update(EntityManagerInterface $entityManager, int $id,
        #[MapRequestPayload(acceptFormat: 'json', validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] GameData $gameData): JsonResponse
    {
        $repository = $entityManager->getRepository(Game::class);
        $game = $repository->find($id);
        if ($game == null)
            return static::json(null, Response::HTTP_NOT_FOUND);
        try {
            $game->initFromData($entityManager, $gameData);
        } catch (Exception $e) {
            return static::json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        $entityManager->persist($game);
        $entityManager->flush();
        return static::json($game, Response::HTTP_OK);
    }

    /**
     * Deletes a game.
     *
     * @param EntityManagerInterface $entityManager
     * @param int $id The identifier of the game.
     * @return JsonResponse
     */
    #[Route('/games/{id}', methods: ['DELETE'])]
    public function Delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $repository = $entityManager->getRepository(Game::class);
        $game = $repository->find($id);
        if ($game == null)
            return static::json(null, Response::HTTP_NOT_FOUND);
        $entityManager->remove($game);
        $entityManager->flush();
        return static::json(null, Response::HTTP_NO_CONTENT);
    }
}