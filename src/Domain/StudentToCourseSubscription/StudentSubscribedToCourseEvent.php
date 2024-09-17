<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\StudentToCourseSubscription;

#[DomainEvent(name: 'student-to-course-subscription.student-subscribed-to-course')]
final readonly class StudentSubscribedToCourseEvent
{
    public function __construct(
        #[DomainId]
        public string $courseId,
        #[DomainId]
        public string $studentId,
    ) {}
}
