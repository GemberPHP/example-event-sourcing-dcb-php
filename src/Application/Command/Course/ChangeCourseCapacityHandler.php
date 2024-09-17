<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Application\Command\Course;

use Gember\ExampleEventSourcingDcb\Domain\Course\ChangeCourseCapacity;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseId;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseNotFoundException;

final readonly class ChangeCourseCapacityHandler
{
    /**
     * @param ContextRepository<ChangeCourseCapacity> $repository
     */
    public function __construct(
        private ContextRepository $repository,
    ) {}

    /**
     * @throws CourseNotFoundException
     */
    public function __invoke(ChangeCourseCapacityCommand $command): void
    {
        $decision = $this->repository->get(ChangeCourseCapacity::class, new CourseId($command->courseId));

        $decision->changeCapacity($command->capacity);

        $this->repository->save($decision);
    }
}
