<?php

namespace App\Controller;

use App\Dto\CompanyRequest;
use App\Dto\CompanySearchRequest;
use App\Dto\PaginatedResponse;
use App\Dto\ProjectSearchRequest;
use App\Entity\Company;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use OpenApi\Annotations as OA;

use App\Repository\CompanyRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraint;
use App\Dto\ProjectRequest;

#[Route('/api/projects')]
class ProjectController extends AbstractController
{
    /**
     * @OA\Get(
     *   summary="List projects with pagination and optional name search",
     *   @OA\Tag(name="Project"),
     *   tags={"Project"},
     *   description="Returns a paginated list of projects.",
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
     *       description="Paginated result of project",
     *       @OA\JsonContent(
     *          @OA\Property(property="page", type="integer"),
     *          @OA\Property(property="limit", type="integer"),
     *          @OA\Property(property="totalItems", type="integer"),
     *          @OA\Property(property="totalPages", type="integer"),
     *          @OA\Property(
     *              property="items",
     *              type="array",
     *              @OA\Items(ref=@Model(type=Project::class))
     *          )
     *       )
     *   )
     * )
     *
     */
    #[Route('', name: 'project_index', methods: ['GET'])]
    public function index(
        #[MapRequestPayload] ProjectSearchRequest $searchDto,
        ProjectRepository $repository
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
                Project::PROJECT_LIST,
                PaginatedResponse::DEFAULT_GROUP
            ]
        ]);
    }

    /**
     * Create a new Project.
     *
     * @OA\Post(
     *     summary="Create an Project",
     *     security={{"basicAuth":{}}},
     *     @OA\Tag(name="Project"),
     * )
     * @OA\RequestBody(
     *     required=true,
     *     description="JSON Payload for creating an Project",
     *     content=new OA\JsonContent(
     *         ref=new Model(type=ProjectRequest::class, groups={"project:write"})
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Project created successfully",
     *     content=new OA\JsonContent(ref=new Model(type=Project::class, groups={"project:read"}))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation errors"
     * )
     */
    #[Route('', name: 'project_create', methods: ['POST'])]
    public function create(
        ManagerRegistry $doctrine,
        #[MapRequestPayload(validationGroups: [
            ProjectRequest::CREATE,
            Constraint::DEFAULT_GROUP
        ])] ProjectRequest $dto
    ): JsonResponse {
        // Create entity
        $project = new Project();
        $project
            ->setName($dto->getName());

        $em = $doctrine->getManager();
        $em->persist($project);
        $em->flush();

        return $this->json($project, Response::HTTP_CREATED, [], [
            'groups' => [
                Project::PROJECT_LIST,
            ]
        ]);
    }

    /**
     * Show a specific project by ID.
     *
     * @OA\Get(
     *     summary="Retrieve an project",
     *     description="Look up a single project by its ID.",
     *     security={{"basicAuth":{}}},
     *     @OA\Tag(name="Project"),
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="project ID",
     *     required=true,
     *     @OA\Schema(type="integer", example=1)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Found the project",
     *     content=new OA\JsonContent(ref=new Model(type=Project::class, groups={"project:read"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Project not found"
     * )
     */
    #[Route('/{id}', name: 'project_show', methods: ['GET'])]
    public function show(
        #[MapEntity] Project $project,
    ): JsonResponse {
        return $this->json($project, Response::HTTP_OK, [], [
            'groups' => [
                Project::PROJECT_LIST,
            ]
        ]);
    }

    /**
     * Update an existing project.
     *
     * You can perform a full (PUT) or partial (PATCH) update.
     *
     * @OA\Put(
     *     summary="Replace an project",
     *     description="Use PUT to fully replace the project details.",
     *     security={{"basicAuth":{}}},
     *     @OA\Tag(name="Project"),
     * )
     * @OA\Patch(
     *     summary="Partially update an project",
     *     description="Use PATCH to partially update one or more fields."
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Project ID",
     *     required=true,
     *     @OA\Schema(type="integer", example=1)
     * )
     * @OA\RequestBody(
     *     required=true,
     *     description="Project details JSON",
     *     content=new OA\JsonContent(
     *         ref=new Model(type=ProjectRequest::class, groups={"project:write"})
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Updated project",
     *     content=new OA\JsonContent(ref=new Model(type=Project::class, groups={"project:read"}))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation errors"
     * )
     * @OA\Response(
     *     response=404,
     *     description="project not found"
     * )
     */
    #[Route('/{id}', name: 'project_update', methods: ['PUT', 'PATCH'])]
    public function update(
        #[MapEntity] Project $project,
        ManagerRegistry $doctrine,
        #[MapRequestPayload(validationGroups: [
            Constraint::DEFAULT_GROUP
        ])] ProjectRequest $dto
    ): JsonResponse {
        $em = $doctrine->getManager();

        $project
            ->setName($dto->getName());

        $em->flush();

        return $this->json($project, Response::HTTP_OK, [], [
            'groups' => [
                Project::PROJECT_LIST,
            ]
        ]);
    }

    /**
     * Delete an project by ID.
     *
     * @OA\Delete(
     *     summary="Delete an project",
     *     description="Removes the specified project entity.",
     *     security={{"basicAuth":{}}},
     *     @OA\Tag(name="Project"),
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Project ID",
     *     required=true,
     *     @OA\Schema(
     *         type="integer",
     *         example=1
     *     )
     * )
     * @OA\Response(
     *     response=204,
     *     description="Project successfully deleted"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Project not found"
     * )
     */
    #[Route('/{id}', name: 'project_delete', methods: ['DELETE'])]
    public function delete(
        #[MapEntity] Project $project,
        ManagerRegistry $doctrine
    ): JsonResponse {
        $em = $doctrine->getManager();

        $em->remove($project);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
