<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\Course;

final class ChangeCourseCapacity implements EventSourcedContext
{
    use EventSourcedContextBehaviorTrait;

    #[EntityId]
    private CourseId $courseId;

    private int $capacity;

    /**
     * @throws CourseNotFoundException
     */
    public function changeCapacity(int $capacity): void
    {
        if ($this->capacity === $capacity) {
            return;
        }

        if (!isset($this->courseId)) {
            throw CourseNotFoundException::create();
        }

        $this->apply(new CourseCapacityChangedEvent((string) $this->courseId, $capacity));
    }

    #[DomainEventSubscriber]
    private function onCourseCreatedEvent(CourseCreatedEvent $event): void
    {
        $this->courseId = new CourseId($event->courseId);
    }

    #[DomainEventSubscriber]
    private function onCourseCapacityChangedEvent(CourseCapacityChangedEvent $event): void
    {
        $this->capacity = $event->capacity;
    }
}
