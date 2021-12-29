<?php

/**
 * Интерфейс нужен пока не переехали на PHP 7.2
 * после переезда можно заюзать @see https://github.com/php-fig/event-dispatcher
 *
 * Defines a dispatcher for events.
 */
interface Doctrine_EventDispatcher_ListenerProviderInterface
{
    /**
     * @param object $event
     *   An event for which to return the relevant listeners.
     * @return iterable<callable>
     *   An iterable (array, iterator, or generator) of callables.  Each
     *   callable MUST be type-compatible with $event.
     */
    public function getListenersForEvent($event) : iterable;
}
