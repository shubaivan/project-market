<?php

namespace App\Controller;

use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/projects')]
class ProjectController extends AbstractController
{
    #[Route('', name: 'project_index', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $repo = $doctrine->getRepository(Project::class);
        $projects = $repo->findAll();

        return $this->json($projects);
    }

    #[Route('', name: 'project_create', methods: ['POST'])]
    public function create(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $project = new Project();
        $project->setName($data['name'] ?? '');

        // Persist entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($project);
        $entityManager->flush();

        return $this->json($project, 201);
    }

    #[Route('/{id}', name: 'project_show', methods: ['GET'])]
    public function show(int $id, ManagerRegistry $doctrine): JsonResponse
    {
        $project = $doctrine->getRepository(Project::class)->find($id);

        if (!$project) {
            return $this->json(['error' => 'Project not found'], 404);
        }

        return $this->json($project);
    }

    #[Route('/{id}', name: 'project_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            return $this->json(['error' => 'Project not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $project->setName($data['name'] ?? $project->getName());

        $entityManager->flush();

        return $this->json($project);
    }

    #[Route('/{id}', name: 'project_delete', methods: ['DELETE'])]
    public function delete(int $id, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            return $this->json(['error' => 'Project not found'], 404);
        }

        $entityManager->remove($project);
        $entityManager->flush();

        return $this->json(null, 204);
    }
}
