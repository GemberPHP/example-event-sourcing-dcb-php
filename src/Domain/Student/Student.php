<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\Student;

/*
 * Traditional aggregate root.
 */
final class Student implements EventSourcedContext
{
    use EventSourcedContextBehaviorTrait;

    /*
     * Define to which domain identifiers this context belongs to.
     */
    #[DomainId]
    private StudentId $studentId;

    public static function create(StudentId $studentId): self
    {
        $student = new self();
        $student->apply(new StudentCreatedEvent((string) $studentId));

        return $student;
    }

    /*
     * Change internal state by subscribing to relevant domain events for any of the domain identifiers,
     * so that this context can apply its business rules.
     */
    #[DomainEventSubscriber]
    private function onStudentCreatedEvent(StudentCreatedEvent $event): void
    {
        $this->studentId = new StudentId($event->studentId);
    }
}
