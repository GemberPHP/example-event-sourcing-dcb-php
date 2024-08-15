<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Application\Command\Course;

use Gember\ExampleEventSourcingDcb\Domain\Course\ChangeCourseCapacityDecision;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseId;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseNotFoundException;

final readonly class ChangeCourseCapacityHandler
{
    /**
     * @param EntityRepository<ChangeCourseCapacityDecision> $repository
     */
    public function __construct(
        private EntityRepository $repository,
    ) {}

    /**
     * @throws CourseNotFoundException
     */
    public function __invoke(ChangeCourseCapacityCommand $command): void
    {
        $decision = $this->repository->get(ChangeCourseCapacityDecision::class, new CourseId($command->courseId));

        $decision->changeCapacity($command->capacity);

        $this->repository->save($decision);
    }
}
