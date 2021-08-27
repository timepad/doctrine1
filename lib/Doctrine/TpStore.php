<?php

/**
 * Doctrine_TpStore
 * 
 * Class for custom column type tpstore. Combine json type and serializiable array type
 *
 * @package     Doctrine
 * @subpackage  TpStore
 * @link        www.doctrine-project.org
 * @since       1.3.11
 */
class Doctrine_TpStore {
    /** @var string  */
    public const TYPE_JSON = 'json';

    /** @var string  */
    public const TYPE_SERIALIZED = 'array';
}
