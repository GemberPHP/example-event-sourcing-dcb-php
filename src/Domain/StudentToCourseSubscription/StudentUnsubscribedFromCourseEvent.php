<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\StudentToCourseSubscription;

#[DomainEvent(name: 'course-subscribing.student-unsubscribed-from-event')]
final readonly class StudentUnsubscribedFromCourseEvent
{
    public function __construct(
        #[EntityId]
        public string $courseId,
        #[EntityId]
        public string $studentId,
    ) {}
}
