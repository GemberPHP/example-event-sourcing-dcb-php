<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\Subscribing;

use Gember\ExampleEventSourcingDcb\Domain\Course\CourseCreatedEvent;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseId;
use Gember\ExampleEventSourcingDcb\Domain\Course\CourseNotFoundException;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentCreatedEvent;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentId;
use Gember\ExampleEventSourcingDcb\Domain\Student\StudentNotFoundException;

final class UnsubscribeStudentFromCourseDecision implements EventSourcedEntity
{
    use EventSourcedEntityBehaviorTrait;

    #[EntityId]
    private CourseId $courseId;
    #[EntityId]
    private StudentId $studentId;

    private bool $isSubscribed;

    /**
     * @throws CourseNotFoundException
     * @throws StudentNotFoundException
     */
    public function unsubscribe(): void
    {
        if (!$this->isSubscribed) {
            return;
        }

        if (!isset($this->courseId)) {
            throw CourseNotFoundException::create();
        }

        if (!isset($this->studentId)) {
            throw StudentNotFoundException::create();
        }

        $this->apply(new StudentUnsubscribedFromCourseEvent((string) $this->courseId, (string) $this->studentId));
    }

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
