<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Application\Command\Subscribing;

/**
 * @see SubscribeStudentToCourseHandler
 */
final readonly class UnsubscribeStudentFromCourseCommand
{
    public function __construct(
        public string $studentId,
        public string $courseId,
    ) {}
}
