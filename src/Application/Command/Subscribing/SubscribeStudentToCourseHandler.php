<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Application\Command\Subscribing;

use Gember\ExampleEventSourcingDcb\Domain\Course\CourseId;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseNotFoundException;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentId;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentNotFoundException;
use Gember\ExampleEventSourcingDcb\Domain\Subscribing\CourseCannotAcceptMoreStudentsException;
use Gember\ExampleEventSourcingDcb\Domain\Subscribing\StudentCannotSubscribeToMoreCoursesException;
use Gember\ExampleEventSourcingDcb\Domain\Subscribing\SubscribeStudentToCourseDecision;

final readonly class SubscribeStudentToCourseHandler
{
    /**
     * @param EntityRepository<SubscribeStudentToCourseDecision> $repository
     */
    public function __construct(
        private EntityRepository $repository,
    ) {}

    /**
     * @throws CourseCannotAcceptMoreStudentsException
     * @throws CourseNotFoundException
     * @throws StudentCannotSubscribeToMoreCoursesException
     * @throws StudentNotFoundException
     */
    public function __invoke(SubscribeStudentToCourseCommand $command): void
    {
        $decision = $this->repository->get(
            SubscribeStudentToCourseDecision::class,
            new CourseId($command->courseId),
            new StudentId($command->studentId),
        );

        $decision->subscribe();

        $this->repository->save($decision);
    }
}
