<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\Student;

#[DomainEvent(name: 'student.created')]
final readonly class StudentCreatedEvent
{
    public function __construct(
        #[EntityId]
        public string $studentId,
    ) {}
}
