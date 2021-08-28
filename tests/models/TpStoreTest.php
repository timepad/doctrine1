<?php

/**
 * @property int id
 * @property array tpstore_column
 */
class TpStoreTest extends Doctrine_Record
{
    public function setTableDefinition() {
        $this->hasColumn('id', 'integer', 11, ['primary' => true, 'autoincrement' => true]);
        $this->hasColumn('tpstore_column', 'tpstore');
    }
}
