<?php

/**
 * Интерфейс нужен пока не переехали на PHP 7.2
 * после переезда можно заюзать @see https://github.com/php-fig/event-dispatcher
 *
 * Defines a dispatcher for events.
 */
interface Doctrine_EventDispatcher_DispatcherInterface
{
    /**
     * Provide all relevant listeners with an event to process.
     *
     * @param object $event
     *   The object to process.
     *
     * @return object
     *   The Event that was passed, now modified by listeners.
     */
    public function dispatch($event);
}
