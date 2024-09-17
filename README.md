# example-event-sourcing-dcb-php
Example project (Proof of Concept) with Event Sourcing in PHP using the 'Dynamic Consistency Boundary' pattern (DCB).

There are currently no actual (PHP) libraries yet following this pattern (as it is still conceptual, and maybe controversial).

## Background
The 'Dynamic Consistency Boundary' pattern is a thought process introduced by Sara Pellegrini in 2023,
explained in her talk ["The Aggregate is dead. Long live the Aggregate!"](https://sara.event-thinking.io/2023/04/kill-aggregate-chapter-1-I-am-here-to-kill-the-aggregate.html). 

Currently, Event Sourced applications rely on a consistent boundary of aggregates, as defined in the 'blue book':

> Cluster the ENTITIES and VALUE OBJECTS into AGGREGATES and define boundaries around each.
Choose one ENTITY to be the root of each AGGREGATE, and control all access to the objects inside the boundary through the root. Allow external objects to hold reference to the root only.

_From "Domain-Driven Design: Tackling Complexity in the Heart of Software" by Eric Evans (the 'blue book')_

### Reusable domain events
This consistent boundary enforces a strict 1-on-1 relation between an aggregate and a domain event. 
It makes the aggregate internally consistent.

The DCB pattern removes this strict 1-on-1 relation. Instead, a domain event is considered as a _Pure event_.
This allows us to create business decision/domain models based on a **subset of specific domain events** as well as from **multiple domain identities**.

These business decision models fit very well in how [EventStorming](https://github.com/ddd-crew/eventstorming-glossary-cheat-sheet) considers aggregates currently.
Instead of aggregates, in EventStorming, these are now called 'system' or 'consistent business rule', 
as the term 'aggregate' is difficult to explain for non-technical actors in a software design process.

### No aggregate synchronisation nor domain services needed
As a domain event can be used by multiple business decision models, there is no synchronization needed between aggregates.
Duplicated events as well as domain services (typically to handle business logic spanning multiple aggregates) are not needed anymore.

### Concurrency problems
As an aggregate is based on internal consistency, two concurrent modification are impossible, 
even when the modified data is (domain-wise) independent of each other. 
The DCB pattern removes this blocking behavior when handled by multiple business decision models.

**But overall it removes accidental complexity which aggregates introduced, allowing to build software closer to the real world.**

More details, pros and cons explained (highly recommended):
- https://sara.event-thinking.io/2023/04/kill-aggregate-chapter-1-I-am-here-to-kill-the-aggregate.html
- https://www.youtube.com/watch?v=wXt54BawI-8

## This example project
_The DCB pattern is an interesting concept, but this does not advocate to remove aggregates completely.
Instead, a hybrid solution with aggregates and business decision models is probably more likely, depending on your domain._

This example project is using a fictive domain (taken from Sara Pellegrini's blog) where students can subscribe to courses (of any kind).
Deliberately this is all what is defined for this domain, to focus on how this could be implemented when using Event Sourcing with the DCB pattern in mind.

It contains both classic aggregates (e.g. [Course](src/Domain/Course/Course.php), [Student](src/Domain/Student/Student.php)) as well as business decision models (e.g. [ChangeCourseCapacity](src/Domain/Course/ChangeCourseCapacity.php), [SubscribeStudentToCourse](src/Domain/StudentToCourseSubscription/SubscribeStudentToCourse.php), [UnsubscribeStudentFromCourse](src/Domain/StudentToCourseSubscription/UnsubscribeStudentFromCourse.php)).

Inspired by other PHP libraries such as [Broadway](https://github.com/broadway), [EventSauce](https://github.com/EventSaucePHP), [Prooph](https://github.com/prooph) and [Ecotone](https://github.com/ecotoneframework) as well as [Axon Framework](https://github.com/AxonFramework) for Java.

### Domain code only
This project does not (yet) define the internals of an event store, as it is actually interesting to look at how to implement the DCB pattern in a business domain; 
how does a business decision model look like? How does it relate to a 'classic' aggregate? 

If the event store implementation (and all of its required related code) would be part of this exercise, its easy to get bogged down into technical details instead.

_Next step: the event store implementation_ 😎