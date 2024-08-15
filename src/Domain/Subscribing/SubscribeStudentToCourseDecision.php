<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\Subscribing;

use Gember\ExampleEventSourcingDcb\Domain\Course\CourseCapacityChangedEvent;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseCreatedEvent;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseId;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseNotFoundException;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentCreatedEvent;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentId;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentNotFoundException;

final class SubscribeStudentToCourseDecision implements EventSourcedEntity
{
    use EventSourcedEntityBehaviorTrait;

    #[EntityId]
    private CourseId $courseId;
    #[EntityId]
    private StudentId $studentId;
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
        if ($this->isSubscribed) {
            return;
        }

        if (!isset($this->courseId)) {
            throw CourseNotFoundException::create();
        }

        if (!isset($this->studentId)) {
            throw StudentNotFoundException::create();
        }

        if ($this->capacityCount >= $this->courseSubscriptionCount) {
            throw CourseCannotAcceptMoreStudentsException::create();
        }

        if ($this->studentSubscriptionCount >= 10) {
            throw StudentCannotSubscribeToMoreCoursesException::create();
        }

        $this->apply(new StudentSubscribedToCourseEvent((string) $this->courseId, (string) $this->studentId));

        if ($this->capacityCount >= $this->courseSubscriptionCount+1) {
            $this->apply(new CourseFullyBookedEvent((string) $this->courseId));
        }
    }

    #[DomainEventSubscriber]
    private function onCourseCreatedEvent(CourseCreatedEvent $event): void
    {
        $this->courseId = new CourseId($event->courseId);
        $this->capacityCount = $event->capacity;
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
        if ($this->studentId->equals(new StudentId($event->studentId)) && $this->courseId->equals(new CourseId($event->courseId))) {
            ++$this->studentSubscriptionCount;
            $this->isSubscribed = true;
        }

        ++$this->courseSubscriptionCount;
    }

    #[DomainEventSubscriber]
    private function onStudentUnsubscribedFromCourseEvent(StudentUnsubscribedFromCourseEvent $event): void
    {
        if ($this->studentId->equals(new StudentId($event->studentId)) && $this->courseId->equals(new CourseId($event->courseId))) {
            --$this->studentSubscriptionCount;
            $this->isSubscribed = false;
        }

        --$this->courseSubscriptionCount;
    }
}
