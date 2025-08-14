<?php

namespace App\Controller;

use App\Dto\CompanyRequest;
use App\Dto\CompanySearchRequest;
use App\Dto\PaginatedResponse;
use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Annotations as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraint;

#[Route('/api/companies')]
class CompanyController extends AbstractController
{
    /**
     * @OA\Get(
     *   summary="List company with pagination and optional name search",
     *   @OA\Tag(name="Company"),
     *   tags={"Company"},
     *   description="Returns a paginated list of companies.",
     *   @OA\SecurityRequirement(name="basicAuth"),
     *   @OA\Parameter(
     *       name="page",
     *       in="query",
     *       description="Page number (1-based), default=1",
     *       required=false,
     *       @OA\Schema(type="integer", default=1)
     *   ),
     *   @OA\Parameter(
     *       name="limit",
     *       in="query",
     *       description="Items per page, default=10",
     *       required=false,
     *       @OA\Schema(type="integer", default=10)
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Paginated result of employees",
     *       @OA\JsonContent(
     *          @OA\Property(property="page", type="integer"),
     *          @OA\Property(property="limit", type="integer"),
     *          @OA\Property(property="totalItems", type="integer"),
     *          @OA\Property(property="totalPages", type="integer"),
     *          @OA\Property(
     *              property="items",
     *              type="array",
     *              @OA\Items(ref=@Model(type=Company::class))
     *          )
     *       )
     *   )
     * )
     *
     */
    #[Route('', name: 'company_index', methods: ['GET'])]
    public function index(
        #[MapRequestPayload] CompanySearchRequest $searchDto,
        CompanyRepository $repository
    ): JsonResponse {
        $employees = $repository->findByCriteria(
            $searchDto->getName(),
            $searchDto->getPage(),
            $searchDto->getLimit()
        );

        $totalItems = $repository->countByCriteria($searchDto->getName());

        $responseDto = new PaginatedResponse(
            $searchDto->getPage(),
            $searchDto->getLimit(),
            $totalItems,
            $employees
        );

        return $this->json($responseDto, Response::HTTP_OK, [], [
            'groups' => [
                Company::COMPANY_LIST,
                PaginatedResponse::DEFAULT_GROUP
            ]
        ]);
    }

    /**
     * Create a new Company.
     *
     * @OA\Post(
     *     summary="Create an Company",
     *     security={{"basicAuth":{}}},
     *     @OA\Tag(name="Company"),
     * )
     * @OA\RequestBody(
     *     required=true,
     *     description="JSON Payload for creating an Company",
     *     content=new OA\JsonContent(
     *         ref=new Model(type=CompanyRequest::class, groups={"company:write"})
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Employee created successfully",
     *     content=new OA\JsonContent(ref=new Model(type=Company::class, groups={"company:read"}))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation errors"
     * )
     */
    #[Route('', name: 'company_create', methods: ['POST'])]
    public function create(
        ManagerRegistry $doctrine,
        #[MapRequestPayload(validationGroups: [
            CompanyRequest::CREATE,
            Constraint::DEFAULT_GROUP
        ])] CompanyRequest $dto
    ): JsonResponse {
        // Create entity
        $company = new Company();
        $company
            ->setName($dto->getName());

        $em = $doctrine->getManager();
        $em->persist($company);
        $em->flush();

        return $this->json($company, Response::HTTP_CREATED, [], [
            'groups' => [
                Company::COMPANY_LIST,
            ]
        ]);
    }

    /**
     * Show a specific company by ID.
     *
     * @OA\Get(
     *     summary="Retrieve an company",
     *     description="Look up a single company by its ID.",
     *     security={{"basicAuth":{}}},
     *     @OA\Tag(name="Company"),
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="company ID",
     *     required=true,
     *     @OA\Schema(type="integer", example=1)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Found the company",
     *     content=new OA\JsonContent(ref=new Model(type=Company::class, groups={"company:read"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Company not found"
     * )
     */
    #[Route('/{id}', name: 'company_show', methods: ['GET'])]
    public function show(
        #[MapEntity] Company $company,
    ): JsonResponse {
        return $this->json($company, Response::HTTP_OK, [], [
            'groups' => [
                Company::COMPANY_LIST,
            ]
        ]);
    }

    /**
     * Update an existing company.
     *
     * You can perform a full (PUT) or partial (PATCH) update.
     *
     * @OA\Put(
     *     summary="Replace an company",
     *     description="Use PUT to fully replace the company details.",
     *     security={{"basicAuth":{}}},
     *     @OA\Tag(name="Company"),
     * )
     * @OA\Patch(
     *     summary="Partially update an company",
     *     description="Use PATCH to partially update one or more fields."
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Company ID",
     *     required=true,
     *     @OA\Schema(type="integer", example=1)
     * )
     * @OA\RequestBody(
     *     required=true,
     *     description="Company details JSON",
     *     content=new OA\JsonContent(
     *         ref=new Model(type=CompanyRequest::class, groups={"company:write"})
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Updated company",
     *     content=new OA\JsonContent(ref=new Model(type=Company::class, groups={"company:read"}))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation errors"
     * )
     * @OA\Response(
     *     response=404,
     *     description="company not found"
     * )
     */
    #[Route('/{id}', name: 'company_update', methods: ['PUT', 'PATCH'])]
    public function update(
        #[MapEntity] Company $company,
        ManagerRegistry $doctrine,
        #[MapRequestPayload(validationGroups: [
            Constraint::DEFAULT_GROUP
        ])] CompanyRequest $dto
    ): JsonResponse {
        $em = $doctrine->getManager();

        $company
            ->setName($dto->getName());

        $em->flush();

        return $this->json($company, Response::HTTP_OK, [], [
            'groups' => [
                Company::COMPANY_LIST,
            ]
        ]);
    }

    /**
     * Delete an company by ID.
     *
     * @OA\Delete(
     *     summary="Delete an company",
     *     description="Removes the specified company entity.",
     *     security={{"basicAuth":{}}},
     *     @OA\Tag(name="Company"),
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Company ID",
     *     required=true,
     *     @OA\Schema(
     *         type="integer",
     *         example=1
     *     )
     * )
     * @OA\Response(
     *     response=204,
     *     description="Company successfully deleted"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Company not found"
     * )
     */
    #[Route('/{id}', name: 'company_delete', methods: ['DELETE'])]
    public function delete(
        #[MapEntity] Company $company,
        ManagerRegistry $doctrine
    ): JsonResponse {
        $em = $doctrine->getManager();

        $em->remove($company);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
