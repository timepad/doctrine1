<?php

final class Doctrine_EventDispatcher_Event_UnserializationFieldFailed
{
    /** @var Doctrine_Table */
    public $table;

    /** @var string */
    public $fieldName;

    /** @var mixed */
    public $fieldValue;

    /** @var array */
    public $recordData;

    public function __construct(Doctrine_Table $table, string $fieldName, $fieldValue, array $recordData)
    {
        $this->table = $table;
        $this->fieldName = $fieldName;
        $this->fieldValue = $fieldValue;
        $this->recordData = $recordData;
    }
}

