<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\StudentToCourseSubscription;

final readonly class CourseFullyBookedEvent
{
    public function __construct(
        #[DomainId]
        public string $courseId,
    ) {}
}
