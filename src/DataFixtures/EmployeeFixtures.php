<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EmployeeFixtures extends Fixture implements DependentFixtureInterface
{
    public const EMPLOYEE_REFERENCE_PREFIX = 'employee_';

    public function load(ObjectManager $manager): void
    {
        // Path to your JSON
        $jsonPath = __DIR__ . '/../../resources/fixtures/employees.json';
        $employeesData = json_decode(file_get_contents($jsonPath), true);

        foreach ($employeesData as $index => $item) {
            $employee = new Employee();
            $employee
                ->setFirstName($item['firstName'])
                ->setLastName($item['lastName'])
                ->setEmail($item['email']);

            // Retrieve the corresponding Company by its reference
            $companyRef = $this->getReference(
                CompanyFixtures::COMPANY_REFERENCE_PREFIX.$item['companyIndex'],
                \App\Entity\Company::class
            );
            $employee->setCompany($companyRef);

            $manager->persist($employee);

            // Store a reference so ProjectFixtures can associate employees
            $this->addReference(
                self::EMPLOYEE_REFERENCE_PREFIX.$index,
                $employee
            );
        }

        $manager->flush();
    }

    // Ensures that companies are loaded first
    public function getDependencies(): array
    {
        return [
            CompanyFixtures::class,
        ];
    }
}
