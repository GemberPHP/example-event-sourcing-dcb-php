<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\StudentToCourseSubscription;

use Gember\ExampleEventSourcingDcb\Domain\Course\CourseCreatedEvent;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseId;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseNotFoundException;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentCreatedEvent;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentId;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentNotFoundException;

/*
 * Business decision model based on multiple domain identifiers.
 */
final class UnsubscribeStudentFromCourse implements EventSourcedContext
{
    use EventSourcedContextBehaviorTrait;

    /*
     * Define to which domain identifiers this context belongs to.
     */
    #[DomainId]
    private CourseId $courseId;
    #[DomainId]
    private StudentId $studentId;

    /*
     * Use private properties to guard idempotency and protect invariants.
     */
    private bool $isSubscribed;

    /**
     * @throws CourseNotFoundException
     * @throws StudentNotFoundException
     */
    public function unsubscribe(): void
    {
        /*
         * Guard for idempotency.
         */
        if (!$this->isSubscribed) {
            return;
        }

        /*
         * Protect invariants (business rules).
         */
        if (!isset($this->courseId)) {
            throw CourseNotFoundException::create();
        }

        if (!isset($this->studentId)) {
            throw StudentNotFoundException::create();
        }

        /*
         * Apply events when all business rules are met.
         */
        $this->apply(new StudentUnsubscribedFromCourseEvent((string) $this->courseId, (string) $this->studentId));
    }

    /*
     * Change internal state by subscribing to relevant domain events for any of the domain identifiers,
     * so that this context can apply its business rules.
     */
    #[DomainEventSubscriber]
    private function onCourseCreatedEvent(CourseCreatedEvent $event): void
    {
        $this->courseId = new CourseId($event->courseId);
    }

    #[DomainEventSubscriber]
    private function onStudentCreatedEvent(StudentCreatedEvent $event): void
    {
        $this->studentId = new StudentId($event->studentId);
        $this->isSubscribed = false;
    }

    #[DomainEventSubscriber]
    private function onStudentSubscribedToCourseEvent(StudentSubscribedToCourseEvent $event): void
    {
        if ($this->studentId->equals(new StudentId($event->studentId)) && $this->courseId->equals(new CourseId($event->courseId))) {
            $this->isSubscribed = true;
        }
    }

    #[DomainEventSubscriber]
    private function onStudentUnsubscribedFromCourseEvent(StudentUnsubscribedFromCourseEvent $event): void
    {
        if ($this->studentId->equals(new StudentId($event->studentId)) && $this->courseId->equals(new CourseId($event->courseId))) {
            $this->isSubscribed = false;
        }
    }
}
