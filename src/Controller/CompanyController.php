<?php

namespace App\Controller;

use App\Entity\Company;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/companies')]
class CompanyController extends AbstractController
{
    #[Route('', name: 'company_index', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $companyRepository = $doctrine->getRepository(Company::class);
        $companies = $companyRepository->findAll();

        // Return the list of companies in JSON.
        // For a real API, you might integrate Symfony serializer or a DTO approach.
        return $this->json($companies);
    }

    #[Route('', name: 'company_create', methods: ['POST'])]
    public function create(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        // Retrieve data from request body (JSON assumed).
        $data = json_decode($request->getContent(), true);

        $entityManager = $doctrine->getManager();
        $company = new Company();
        $company->setName($data['name'] ?? '');

        $entityManager->persist($company);
        $entityManager->flush();

        // Return newly created entity data.
        return $this->json($company, 201);
    }

    #[Route('/{id}', name: 'company_show', methods: ['GET'])]
    public function show(int $id, ManagerRegistry $doctrine): JsonResponse
    {
        $company = $doctrine->getRepository(Company::class)->find($id);

        if (!$company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        return $this->json($company);
    }

    #[Route('/{id}', name: 'company_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (!$company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $company->setName($data['name'] ?? $company->getName());

        $entityManager->flush();

        return $this->json($company);
    }

    #[Route('/{id}', name: 'company_delete', methods: ['DELETE'])]
    public function delete(int $id, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (!$company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        $entityManager->remove($company);
        $entityManager->flush();

        return $this->json(null, 204);
    }
}
