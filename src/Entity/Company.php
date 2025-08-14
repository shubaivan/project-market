<?php
// src/Entity/Company.php
namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ORM\Table(name: 'company')]
#[UniqueEntity(
    fields: ['name'],
    message: 'This name is already in use.'
)]
class Company
{
    const COMPANY_LIST = 'company_list';
    const COMPANY_READ = 'company:read';
    const COMPANY_WRITE = 'company:write';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([self::COMPANY_READ, self::COMPANY_LIST])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups([self::COMPANY_READ, self::COMPANY_LIST, self::COMPANY_WRITE])]
    private ?string $name = null;

    /**
     * @var Collection<int, Employee>
     */
    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Employee::class, cascade: ['persist', 'remove'])]
    private Collection $employees;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Project::class, cascade: ['persist', 'remove'])]
    private Collection $projects;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
        $this->projects  = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Employee>
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function addEmployee(Employee $employee): self
    {
        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
            $employee->setCompany($this);
        }

        return $this;
    }

    public function removeEmployee(Employee $employee): self
    {
        if ($this->employees->removeElement($employee)) {
            // Set the owning side to null (unless already changed)
            if ($employee->getCompany() === $this) {
                $employee->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setCompany($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // Set the owning side to null (unless already changed)
            if ($project->getCompany() === $this) {
                $project->setCompany(null);
            }
        }

        return $this;
    }
}
