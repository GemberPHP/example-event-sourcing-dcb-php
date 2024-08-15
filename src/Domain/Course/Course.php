<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\Course;

final class Course implements EventSourcedEntity
{
    use EventSourcedEntityBehaviorTrait;

    #[EntityId]
    private CourseId $courseId;
    private string $name;

    public static function create(CourseId $courseId, string $name, int $capacity): self
    {
        $course = new self();
        $course->apply(new CourseCreatedEvent((string) $courseId, $name, $capacity));

        return $course;
    }

    public function changeName(string $name): void
    {
        if ($this->name === $name) {
            return;
        }

        $this->apply(new CourseNameChangedEvent((string) $this->courseId, $name));
    }

    #[DomainEventSubscriber]
    private function onCourseCreatedEvent(CourseCreatedEvent $event): void
    {
        $this->courseId = new CourseId($event->courseId);
        $this->name = $event->name;
    }

    #[DomainEventSubscriber]
    private function onCourseNameChangedEvent(CourseNameChangedEvent $event): void
    {
        $this->name = $event->name;
    }
}
