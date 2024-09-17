<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\Course;

#[DomainEvent(name: 'course.name-changed')]
final readonly class CourseNameChangedEvent
{
    public function __construct(
        #[DomainId]
        public string $courseId,
        public string $name,
    ) {}
}
