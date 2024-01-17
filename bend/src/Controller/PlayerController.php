<?php

namespace App\Controller;

use App\DataTransferObject\PlayerData;
use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{
    /**
     * Creates a player.
     *
     * @param EntityManagerInterface $entityManager
     * @param PlayerData $playerData The data of the player to be created.
     * @return JsonResponse
     */
    #[Route('/players', methods: ['POST'], format: 'json')]
    public function Create(
        EntityManagerInterface $entityManager,
        #[MapRequestPayload(acceptFormat: 'json', validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] PlayerData $playerData
    ): JsonResponse {
        $player = new Player();
        try {
            $player->initFromData($entityManager, $playerData);
        } catch (Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        $entityManager->persist($player);
        $entityManager->flush();
        return $this->json($player, Response::HTTP_CREATED);
    }

    /**
     * Retrieves a player.
     *
     * @param EntityManagerInterface $entityManager
     * @param int $id The identifier of the player.
     * @return JsonResponse
     */
    #[Route('/players/{id}', methods: ['GET'])]
    public function Read(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $repository = $entityManager->getRepository(Player::class);
        $player = $repository->find($id);
        if ($player === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }
        return $this->json($player, Response::HTTP_OK);
    }

    /**
     * Retrieves all the players.
     *
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/players', methods: ['GET'])]
    public function List(EntityManagerInterface $entityManager): JsonResponse
    {
        $repository = $entityManager->getRepository(Player::class);
        $playerList = $repository->findAll();
        return $this->json($playerList, Response::HTTP_OK);
    }

    /**
     * Updates a player.
     *
     * @param EntityManagerInterface $entityManager
     * @param int $id The identifier of the player.
     * @param PlayerData $playerData The data of the player to be updated.
     * @return JsonResponse
     */
    #[Route('/players/{id}', methods: ['PUT'], format: 'json')]
    public function Update(
        EntityManagerInterface $entityManager,
        int $id,
        #[MapRequestPayload(acceptFormat: 'json', validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] PlayerData $playerData
    ): JsonResponse {
        $repository = $entityManager->getRepository(Player::class);
        $player = $repository->find($id);
        if ($player === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }
        try {
            $player->initFromData($entityManager, $playerData);
        } catch (Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        $entityManager->persist($player);
        $entityManager->flush();
        return $this->json($player, Response::HTTP_OK);
    }

    /**
     * Deletes a player.
     *
     * @param EntityManagerInterface $entityManager
     * @param int $id The identifier of the player.
     * @return JsonResponse
     */
    #[Route('/players/{id}', methods: ['DELETE'])]
    public function Delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $repository = $entityManager->getRepository(Player::class);
        $player = $repository->find($id);
        if ($player === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($player);
        $entityManager->flush();
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}