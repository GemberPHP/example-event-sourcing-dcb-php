<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\Course;

#[DomainEvent(name: 'course.created')]
final readonly class CourseCreatedEvent
{
    public function __construct(
        #[DomainId]
        public string $courseId,
        public string $name,
        public int $capacity,
    ) {}
}
