<?php

declare(strict_types=1);

namespace Gember\ExampleEventSourcingDcb\Application\Command\Course;

/**
 * @see ChangeCourseNameHandler
 */
final readonly class ChangeCourseNameCommand
{
    public function __construct(
        public string $courseId,
        public string $name,
    ) {}
}
