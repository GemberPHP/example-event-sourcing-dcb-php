<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Domain\Course;

/*
 * Traditional aggregate root.
 */
final class Course implements EventSourcedContext
{
    use EventSourcedContextBehaviorTrait;

    /*
     * Define to which domain identifiers this context belongs to.
     */
    #[DomainId]
    private CourseId $courseId;

    /*
     * Use private properties to guard idempotency and protect invariants.
     */
    private string $name;

    public static function create(CourseId $courseId, string $name, int $capacity): self
    {
        $course = new self();
        $course->apply(new CourseCreatedEvent((string) $courseId, $name, $capacity));

        return $course;
    }

    public function changeName(string $name): void
    {
        /*
         * Guard for idempotency.
         */
        if ($this->name === $name) {
            return;
        }

        /*
         * Apply events when all business rules are met.
         */
        $this->apply(new CourseNameChangedEvent((string) $this->courseId, $name));
    }

    /*
     * Change internal state by subscribing to relevant domain events for any of the domain identifiers,
     * so that this context can apply its business rules.
     */
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
