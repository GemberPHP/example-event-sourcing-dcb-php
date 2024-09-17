<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\Student;

final class Student implements EventSourcedContext
{
    use EventSourcedContextBehaviorTrait;

    #[EntityId]
    private StudentId $studentId;

    public static function create(StudentId $studentId): self
    {
        $student = new self();
        $student->apply(new StudentCreatedEvent((string) $studentId));

        return $student;
    }

    #[DomainEventSubscriber]
    private function onStudentCreatedEvent(StudentCreatedEvent $event): void
    {
        $this->studentId = new StudentId($event->studentId);
    }
}
