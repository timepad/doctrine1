<?php

/**
 * Ничего не диспатчит
 */
final class Doctrine_EventDispatcher_NullDispatcher implements Doctrine_EventDispatcher_DispatcherInterface
{
    /**
     * @inheritDoc
     */
    public function dispatch($event)
    {
        return $event;
    }
}