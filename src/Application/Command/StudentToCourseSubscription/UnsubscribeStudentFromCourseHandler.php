<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Application\Command\StudentToCourseSubscription;

use Gember\ExampleEventSourcingDcb\Domain\Course\CourseId;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseNotFoundException;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentId;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentNotFoundException;
use Gember\ExampleEventSourcingDcb\Domain\StudentToCourseSubscription\UnsubscribeStudentFromCourse;

final readonly class UnsubscribeStudentFromCourseHandler
{
    /**
     * @param ContextRepository<UnsubscribeStudentFromCourse> $repository
     */
    public function __construct(
        private ContextRepository $repository,
    ) {}

    /**
     * @throws CourseNotFoundException
     * @throws StudentNotFoundException
     */
    public function __invoke(UnsubscribeStudentFromCourseCommand $command): void
    {
        $decision = $this->repository->get(
            UnsubscribeStudentFromCourse::class,
            new CourseId($command->courseId),
            new StudentId($command->studentId),
        );

        $decision->unsubscribe();

        $this->repository->save($decision);
    }
}
