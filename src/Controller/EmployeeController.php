<?php

namespace App\Controller;

use App\Dto\EmployeeRequest;
use App\Dto\EmployeeSearchRequest;
use App\Dto\PaginatedEmployeesResponse;
use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Annotations as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraint;

// Nelmio / OpenAPI

#[Route('/api/employees')]
class EmployeeController extends AbstractController
{
    /**
     * @OA\Get(
     *   summary="List employees with pagination and optional email search",
     *   description="Returns a paginated list of employees.
     *                You may filter results by partial email match.",
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
     *   @OA\Parameter(
     *       name="email",
     *       in="query",
     *       description="Filter employees by email (contains match)",
     *       required=false,
     *       @OA\Schema(type="string")
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
     *              @OA\Items(ref=@Model(type=Employee::class))
     *          )
     *       )
     *   )
     * )
     *
     * @OA\Tag(name="Employees")
     */
    #[Route('', name: 'employee_index', methods: ['GET'])]
    public function index(
        #[MapRequestPayload] EmployeeSearchRequest $searchDto,
        EmployeeRepository $repository
    ): JsonResponse {
        $employees = $repository->findByCriteria(
            $searchDto->getEmail(),
            $searchDto->getPage(),
            $searchDto->getLimit()
        );

        $totalItems = $repository->countByCriteria($searchDto->getEmail());

        $responseDto = new PaginatedEmployeesResponse(
            $searchDto->getPage(),
            $searchDto->getLimit(),
            $totalItems,
            $employees
        );

        return $this->json($responseDto);
    }

    /**
     * Create a new employee.
     *
     * @OA\Post(summary="Create an employee")
     * @OA\RequestBody(
     *     required=true,
     *     description="JSON Payload for creating an Employee",
     *     content=new OA\JsonContent(
     *         ref=new Model(type=EmployeeRequest::class, groups={"employee:write"})
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Employee created successfully",
     *     content=new OA\JsonContent(ref=new Model(type=Employee::class, groups={"employee:read"}))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation errors"
     * )
     * @OA\Tag(name="Employees")
     */
    #[Route('', name: 'employee_create', methods: ['POST'])]
    public function create(
        ManagerRegistry $doctrine,
        #[MapRequestPayload(validationGroups: [
            EmployeeRequest::CREATE,
            Constraint::DEFAULT_GROUP
        ])] EmployeeRequest $dto
    ): JsonResponse {
        // Create entity
        $employee = new Employee();
        $employee
            ->setFirstName($dto->getFirstName())
            ->setLastName($dto->getLastName());

        $em = $doctrine->getManager();
        $em->persist($employee);
        $em->flush();

        return $this->json($employee, 201);
    }

    /**
     * Show a specific employee by ID.
     *
     * @OA\Get(
     *     summary="Retrieve an employee",
     *     description="Look up a single Employee by its ID."
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Employee ID",
     *     required=true,
     *     @OA\Schema(type="integer", example=1)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Found the employee",
     *     content=new OA\JsonContent(ref=new Model(type=Employee::class, groups={"employee:read"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Employee not found"
     * )
     * @OA\Tag(name="Employees")
     */
    #[Route('/{id}', name: 'employee_show', methods: ['GET'])]
    public function show(
        #[MapEntity] Employee $employee,
    ): JsonResponse {
        return $this->json($employee);
    }

    /**
     * Update an existing employee.
     *
     * You can perform a full (PUT) or partial (PATCH) update.
     *
     * @OA\Put(
     *     summary="Replace an employee",
     *     description="Use PUT to fully replace the employee details."
     * )
     * @OA\Patch(
     *     summary="Partially update an employee",
     *     description="Use PATCH to partially update one or more fields."
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Employee ID",
     *     required=true,
     *     @OA\Schema(type="integer", example=1)
     * )
     * @OA\RequestBody(
     *     required=true,
     *     description="Employee details JSON",
     *     content=new OA\JsonContent(
     *         ref=new Model(type=EmployeeRequest::class, groups={"employee:write"})
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Updated Employee",
     *     content=new OA\JsonContent(ref=new Model(type=Employee::class, groups={"employee:read"}))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation errors"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Employee not found"
     * )
     * @OA\Tag(name="Employees")
     */
    #[Route('/{id}', name: 'employee_update', methods: ['PUT', 'PATCH'])]
    public function update(
        #[MapEntity] Employee $employee,
        ManagerRegistry $doctrine,
        #[MapRequestPayload(validationGroups: [
            Constraint::DEFAULT_GROUP
        ])] EmployeeRequest $dto
    ): JsonResponse {
        $em = $doctrine->getManager();

        $employee->setFirstName($dto->getFirstName());
        $employee->setLastName($dto->getLastName());

        $em->flush();

        return $this->json($employee);
    }

    /**
     * Delete an employee by ID.
     *
     * @OA\Delete(
     *     summary="Delete an employee",
     *     description="Removes the specified Employee entity."
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Employee ID",
     *     required=true,
     *     @OA\Schema(
     *         type="integer",
     *         example=1
     *     )
     * )
     * @OA\Response(
     *     response=204,
     *     description="Employee successfully deleted"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Employee not found"
     * )
     * @OA\Tag(name="Employees")
     */
    #[Route('/{id}', name: 'employee_delete', methods: ['DELETE'])]
    public function delete(
        #[MapEntity] Employee $employee,
        ManagerRegistry $doctrine
    ): JsonResponse {
        $em = $doctrine->getManager();

        $em->remove($employee);
        $em->flush();

        return $this->json(null, 204);
    }
}
