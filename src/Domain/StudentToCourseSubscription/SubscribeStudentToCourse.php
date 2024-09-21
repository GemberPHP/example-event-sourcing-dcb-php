<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\StudentToCourseSubscription;

use Gember\ExampleEventSourcingDcb\Domain\Course\CourseCapacityChangedEvent;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseCreatedEvent;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseId;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseNotFoundException;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentCreatedEvent;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentId;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentNotFoundException;

/*
 * Business decision model based on multiple domain identifiers.
 */
final class SubscribeStudentToCourse implements EventSourcedContext
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
    private int $capacityCount;
    private int $courseSubscriptionCount;
    private int $studentSubscriptionCount;

    /**
     * @throws CourseCannotAcceptMoreStudentsException
     * @throws CourseNotFoundException
     * @throws StudentCannotSubscribeToMoreCoursesException
     * @throws StudentNotFoundException
     */
    public function subscribe(): void
    {
        /*
         * Guard for idempotency.
         */
        if ($this->isSubscribed) {
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

        if ($this->courseSubscriptionCount >= $this->capacityCount) {
            throw CourseCannotAcceptMoreStudentsException::create();
        }

        if ($this->studentSubscriptionCount >= 10) {
            throw StudentCannotSubscribeToMoreCoursesException::create();
        }

        /*
         * Apply events when all business rules are met.
         */
        $this->apply(new StudentSubscribedToCourseEvent((string) $this->courseId, (string) $this->studentId));

        if ($this->courseSubscriptionCount+1 >= $this->capacityCount) {
            $this->apply(new CourseFullyBookedEvent((string) $this->courseId));
        }
    }

    /*
     * Change internal state by subscribing to relevant domain events for any of the domain identifiers,
     * so that this context can apply its business rules.
     */
    #[DomainEventSubscriber]
    private function onCourseCreatedEvent(CourseCreatedEvent $event): void
    {
        $this->courseId = new CourseId($event->courseId);
        $this->capacityCount = $event->capacity;
        $this->courseSubscriptionCount = 0;
    }

    #[DomainEventSubscriber]
    private function onStudentCreatedEvent(StudentCreatedEvent $event): void
    {
        $this->studentId = new StudentId($event->studentId);
        $this->studentSubscriptionCount = 0;
        $this->isSubscribed = false;
    }

    #[DomainEventSubscriber]
    private function onCourseCapacityChangedEvent(CourseCapacityChangedEvent $event): void
    {
        $this->capacityCount = $event->capacity;
    }

    #[DomainEventSubscriber]
    private function onStudentSubscribedToCourseEvent(StudentSubscribedToCourseEvent $event): void
    {
        if (isset($this->studentId) &&
            $this->studentId->equals(new StudentId($event->studentId)) &&
            $this->courseId->equals(new CourseId($event->courseId))
        ) {
            ++$this->studentSubscriptionCount;
            $this->isSubscribed = true;
        }

        ++$this->courseSubscriptionCount;
    }

    #[DomainEventSubscriber]
    private function onStudentUnsubscribedFromCourseEvent(StudentUnsubscribedFromCourseEvent $event): void
    {
        if (isset($this->studentId) &&
            $this->studentId->equals(new StudentId($event->studentId)) &&
            $this->courseId->equals(new CourseId($event->courseId))
        ) {
            --$this->studentSubscriptionCount;
            $this->isSubscribed = false;
        }

        --$this->courseSubscriptionCount;
    }
}
