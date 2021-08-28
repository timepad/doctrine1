<?php

/**
 * Doctrine_DataType_TpStore_TestCase
 *
 * @package     Doctrine
 * @category    Object Relational Mapping
 * @version     $Revision$
 */
class Doctrine_DataType_TpStore_TestCase extends Doctrine_UnitTestCase {
    /** @var TpStoreTest */
    private $model;

    /** @var array  */
    private const VALUE = ['test_key' => 'test_val', 11 => 22];

    /** @var string[]  */
    private const FN_TO_TYPE = [
        Doctrine_TpStore::TYPE_SERIALIZED => 'serialize',
        Doctrine_TpStore::TYPE_JSON => 'json_encode'
    ];

    public function prepareData() {
        $this->model = new TpStoreTest();
        $this->model->tpstore_column = static::VALUE;
        $this->model->save();
    }
    public function prepareTables() {
        $this->tables = array('TpStoreTest');
        parent::prepareTables();
    }

    public function testSerialized(): void {
        $this->baseTest(Doctrine_TpStore::TYPE_SERIALIZED);
    }

    public function testJson(): void {
        $this->baseTest(Doctrine_TpStore::TYPE_JSON);
    }

    private function baseTest(string $checked_type): void {
        $type = $this->model->getBaseTypeFromTpStoreOriginalTypesByFieldname('tpstore_column');
        $name = $this->model->getClass();
        $fn_name = static::FN_TO_TYPE[$checked_type];

        if ($type !== $checked_type) {
            $query = new Doctrine_Query($this->connection);

            try {
                $query->query("UPDATE $name SET tpstore_column = ? WHERE id = ?", [$fn_name(static::VALUE), $this->model->id]);
            } catch (Exception $e) {
                $this->fail();
            }
        }

        /** @var TpStoreTest $test */
        $test = TpStoreTest::getById($this->model->id);

        $this->assertTrue(is_array($test->tpstore_column));
        $this->assertTrue(empty(array_diff($test->tpstore_column, static::VALUE)));

        foreach (static::VALUE as $key => $value) {
            $this->assertEqual($test->tpstore_column[$key], $value);
        }
    }
}