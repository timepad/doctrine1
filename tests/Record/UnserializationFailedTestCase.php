<?php

/**
 * Doctrine_Record_UnserializationFailed_TestCase
 */
class Doctrine_Record_UnserializationFailed_TestCase extends Doctrine_UnitTestCase
{
    public function testUnserializationFailed()
    {
        // устанавливаем кастомный диспатчер
        $manager = Doctrine_Manager::getInstance();
        $prevDispatcher = $manager->getEventDispatcher();
        $myDispatcher = new MyDispatcher();
        $manager->setEventDispatcher($myDispatcher);

        // модель для теста
        $model = new SerializeTest();

        // гидратор для модели
        $hydrator = new MockHydrator(['serialize_table' => ['table' => $model->getTable()]]);

        // настройки, типа после DQL
        $dqlAlias = 'serialize_table';
        $cache = [
            'arraytest' => [
                'fieldName' => 'arraytest',
                'dqlAlias' => $dqlAlias,
                'type' => 'array',
                'isIdentifier' => false,
                'isSimpleType' => false,

            ],
        ];

        // данные из БД
        $cascadeValue = [
            'string' => 'просто строка',
            'array'  => [
                'sub_1' => false,
                'sub_2' => 'вложенная строка',
                'sub_3' => 123,
            ]
        ];
        $serializedCascade = serialize($cascadeValue);

        /**
         * 1. Проверка для корректных данных
         */
        $res = $hydrator->gatherRowData(['arraytest' => $serializedCascade], $cache);
        $this->assertEqual($res[$dqlAlias]['arraytest'], $cascadeValue);

        // в диспатчер ничего не прилетало ибо нет ошибки
        $this->assertEqual($myDispatcher->dispatchedEvent, null);

        /**
         * 2. Проверка, для битых данных возвращаем NULL
         */
        $corruptedCascade = substr($serializedCascade, 0, -5); // ломаем данные
        $res = $hydrator->gatherRowData(['arraytest' => $corruptedCascade], $cache);

        $this->assertEqual($res[$dqlAlias]['arraytest'], null); // <- NULL

        // и в диспатчер fire-нулось событие UnserializationFieldFailed
        $this->assertEqual($myDispatcher->dispatchedEvent, Doctrine_EventDispatcher_Event_UnserializationFieldFailed::class);

        // возвращаем старый диспатчер
        $manager->setEventDispatcher($prevDispatcher);
    }
}

class MockHydrator extends Doctrine_Hydrator_ArrayDriver
{
    public function __construct(array $queryComponent)
    {
        $this->_queryComponents = array_merge($this->_queryComponents, $queryComponent);
    }

    public function gatherRowData(array $data, array $cache)
    {
        $id = [];
        $nonemptyComponents = [];

        return $this->_gatherRowData($data, $cache, $id, $nonemptyComponents);
    }
}

class MyDispatcher implements Doctrine_EventDispatcher_DispatcherInterface
{
    public $dispatchedEvent = false;

    /**
     * @inheritDoc
     */
    public function dispatch($event)
    {
        $this->dispatchedEvent = get_class($event);

        return $event;
    }
}