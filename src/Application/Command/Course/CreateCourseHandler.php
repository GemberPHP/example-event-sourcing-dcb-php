<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Application\Command\Course;

use Gember\ExampleEventSourcingDcb\Domain\Course\Course;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseId;

final readonly class CreateCourseHandler
{
    /**
     * @param ContextRepository<Course> $repository
     */
    public function __construct(
        private ContextRepository $repository,
    ) {}

    public function __invoke(CreateCourseCommand $command): void
    {
        $courseId = new CourseId($command->courseId);

        if ($this->repository->has(Course::class, $courseId)) {
            return;
        }

        $course = Course::create($courseId, $command->name, $command->capacity);

        $this->repository->save($course);
    }
}
