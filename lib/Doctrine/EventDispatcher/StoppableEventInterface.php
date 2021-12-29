<?php

/**
 * Интерфейс нужен пока не переехали на PHP 7.2
 * после переезда можно заюзать @see https://github.com/php-fig/event-dispatcher
 *
 * Defines a dispatcher for events.
 */
interface Doctrine_EventDispatcher_StoppableEventInterface
{
    /**
     * Is propagation stopped?
     *
     * This will typically only be used by the Dispatcher to determine if the
     * previous listener halted propagation.
     *
     * @return bool
     *   True if the Event is complete and no further listeners should be called.
     *   False to continue calling listeners.
     */
    public function isPropagationStopped() : bool;
}