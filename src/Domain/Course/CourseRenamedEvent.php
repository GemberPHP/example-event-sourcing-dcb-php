<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\Course;

#[DomainEvent(name: 'course.renamed')]
final readonly class CourseRenamedEvent
{
    public function __construct(
        #[DomainId]
        public string $courseId,
        public string $name,
    ) {}
}
