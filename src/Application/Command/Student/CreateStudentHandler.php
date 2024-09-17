<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Application\Command\Student;

use Gember\ExampleEventSourcingDcb\Domain\Student\Student;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentId;

final readonly class CreateStudentHandler
{
    /**
     * @param ContextRepository<Student> $repository
     */
    public function __construct(
        private ContextRepository $repository,
    ) {}

    public function __invoke(CreateStudentCommand $command): void
    {
        $studentId = new StudentId($command->studentId);

        if ($this->repository->has(Student::class, $studentId)) {
            return;
        }

        $course = Student::create($studentId);

        $this->repository->save($course);
    }
}
