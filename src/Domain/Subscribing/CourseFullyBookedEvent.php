<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\Subscribing;

final readonly class CourseFullyBookedEvent
{
    public function __construct(
        #[EntityId]
        public string $courseId,
    ) {}
}
