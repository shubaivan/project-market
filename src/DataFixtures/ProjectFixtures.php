<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Path to your JSON
        $jsonPath = __DIR__ . '/../../resources/fixtures/projects.json';
        $projectsData = json_decode(file_get_contents($jsonPath), true);

        foreach ($projectsData as $index => $item) {
            $project = new Project();
            $project->setName($item['name']);

            // Link the Company
            $companyRef = $this->getReference(
                CompanyFixtures::COMPANY_REFERENCE_PREFIX.$item['companyIndex'],
                \App\Entity\Company::class
            );
            $project->setCompany($companyRef);

            // Link employees
            foreach ($item['employees'] as $employeeIndex) {
                $employeeRef = $this->getReference(
                    EmployeeFixtures::EMPLOYEE_REFERENCE_PREFIX.$employeeIndex,
                    \App\Entity\Employee::class
                );
                $project->addEmployee($employeeRef);
            }

            $manager->persist($project);
        }

        $manager->flush();
    }

    // Ensures both companies and employees are loaded before projects
    public function getDependencies(): array
    {
        return [
            CompanyFixtures::class,
            EmployeeFixtures::class,
        ];
    }
}
