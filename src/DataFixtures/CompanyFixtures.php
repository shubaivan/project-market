<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CompanyFixtures extends Fixture
{
    public const COMPANY_REFERENCE_PREFIX = 'company_';

    public function load(ObjectManager $manager): void
    {
        // Path to your JSON
        $jsonPath = __DIR__ . '/../../resources/fixtures/companies.json';
        $companiesData = json_decode(file_get_contents($jsonPath), true);

        foreach ($companiesData as $index => $item) {
            $company = new Company();
            $company->setName($item['name']);

            $manager->persist($company);

            // Store a reference for other fixtures
            $this->addReference(
                self::COMPANY_REFERENCE_PREFIX.$index,
                $company
            );
        }

        $manager->flush();
    }
}
