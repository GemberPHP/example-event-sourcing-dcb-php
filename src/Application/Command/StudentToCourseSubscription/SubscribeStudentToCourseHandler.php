<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Application\Command\StudentToCourseSubscription;

use Gember\ExampleEventSourcingDcb\Domain\Course\CourseId;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseNotFoundException;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentId;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentNotFoundException;
use Gember\ExampleEventSourcingDcb\Domain\StudentToCourseSubscription\CourseCannotAcceptMoreStudentsException;
use Gember\ExampleEventSourcingDcb\Domain\StudentToCourseSubscription\StudentCannotSubscribeToMoreCoursesException;
use Gember\ExampleEventSourcingDcb\Domain\StudentToCourseSubscription\SubscribeStudentToCourse;

final readonly class SubscribeStudentToCourseHandler
{
    /**
     * @param ContextRepository<SubscribeStudentToCourse> $repository
     */
    public function __construct(
        private ContextRepository $repository,
    ) {}

    /**
     * @throws CourseCannotAcceptMoreStudentsException
     * @throws CourseNotFoundException
     * @throws StudentCannotSubscribeToMoreCoursesException
     * @throws StudentNotFoundException
     */
    public function __invoke(SubscribeStudentToCourseCommand $command): void
    {
        $context = $this->repository->get(
            SubscribeStudentToCourse::class,
            new CourseId($command->courseId),
            new StudentId($command->studentId),
        );

        $context->subscribe();

        $this->repository->save($context);
    }
}
