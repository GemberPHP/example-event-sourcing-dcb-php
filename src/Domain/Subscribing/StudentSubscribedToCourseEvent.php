<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\Subscribing;

#[DomainEvent(name: 'course-subscribing.student-subscribed-to-event')]
final readonly class StudentSubscribedToCourseEvent
{
    public function __construct(
        #[EntityId]
        public string $courseId,
        #[EntityId]
        public string $studentId,
    ) {}
}
