<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Application\Command\Course;

use Gember\ExampleEventSourcingDcb\Domain\Course\Course;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseId;

final readonly class RenameCourseHandler
{
    /**
     * @param ContextRepository<Course> $repository
     */
    public function __construct(
        private ContextRepository $repository,
    ) {}

    public function __invoke(RenameCourseCommand $command): void
    {
        $course = $this->repository->get(Course::class, new CourseId($command->courseId));

        $course->rename($command->name);

        $this->repository->save($course);
    }
}
