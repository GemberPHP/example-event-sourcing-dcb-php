<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Application\Command\Subscribing;

use Gember\ExampleEventSourcingDcb\Domain\Course\CourseId;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseNotFoundException;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentId;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentNotFoundException;
use Gember\ExampleEventSourcingDcb\Domain\Subscribing\UnsubscribeStudentFromCourseDecision;

final readonly class UnsubscribeStudentFromCourseHandler
{
    /**
     * @param EntityRepository<UnsubscribeStudentFromCourseDecision> $repository
     */
    public function __construct(
        private EntityRepository $repository,
    ) {}

    /**
     * @throws CourseNotFoundException
     * @throws StudentNotFoundException
     */
    public function __invoke(UnsubscribeStudentFromCourseCommand $command): void
    {
        $decision = $this->repository->get(
            UnsubscribeStudentFromCourseDecision::class,
            new CourseId($command->courseId),
            new StudentId($command->studentId),
        );

        $decision->unsubscribe();

        $this->repository->save($decision);
    }
}
