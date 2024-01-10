<?php

namespace App\Controller;

use App\DataTransferObject\LeagueData;
use App\Entity\League;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class LeagueController extends AbstractController
{
    /**
     * Creates a league.
     *
     * @param EntityManagerInterface $entityManager
     * @param LeagueData $leagueData The data of the league to be created.
     * @return JsonResponse
     */
    #[Route('/leagues', methods: ['POST'], format: 'json')]
    public function Create(EntityManagerInterface $entityManager,
        #[MapRequestPayload(acceptFormat: 'json', validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] LeagueData $leagueData): JsonResponse
    {
        $league = new League();
        try {
            $league->initFromData($entityManager, $leagueData);
        } catch (Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        $entityManager->persist($league);
        $entityManager->flush();
        return $this->json($league, Response::HTTP_CREATED);
    }

    /**
     * Retrieves a league.
     *
     * @param EntityManagerInterface $entityManager
     * @param int $id The identifier of the league.
     * @return JsonResponse
     */
    #[Route('/leagues/{id}', methods: ['GET'])]
    public function Read(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $repository = $entityManager->getRepository(League::class);
        $league = $repository->find($id);
        if ($league === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }
        return $this->json($league, Response::HTTP_OK);
    }

    /**
     * Retrieves all the leagues.
     *
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/leagues', methods: ['GET'])]
    public function List(EntityManagerInterface $entityManager): JsonResponse
    {
        $repository = $entityManager->getRepository(League::class);
        $leagueList = $repository->findAll();
        return $this->json($leagueList, Response::HTTP_OK);
    }

    /**
     * Updates a league.
     *
     * @param EntityManagerInterface $entityManager
     * @param int $id The identifier of the league.
     * @param LeagueData $leagueData The data of the league to be updated.
     * @return JsonResponse
     */
    #[Route('/leagues/{id}', methods: ['PUT'], format: 'json')]
    public function Update(EntityManagerInterface $entityManager, int $id,
        #[MapRequestPayload(acceptFormat: 'json', validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] LeagueData $leagueData): JsonResponse
    {
        $repository = $entityManager->getRepository(League::class);
        $league = $repository->find($id);
        if ($league === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }
        try {
            $league->initFromData($entityManager, $leagueData);
        } catch (Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        $entityManager->persist($league);
        $entityManager->flush();
        return $this->json($league, Response::HTTP_OK);
    }

    /**
     * Deletes a league.
     *
     * @param EntityManagerInterface $entityManager
     * @param int $id The identifier of the league.
     * @return JsonResponse
     */
    #[Route('/leagues/{id}', methods: ['DELETE'])]
    public function Delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $repository = $entityManager->getRepository(League::class);
        $league = $repository->find($id);
        if ($league === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($league);
        $entityManager->flush();
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}