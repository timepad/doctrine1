<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

/**
 * Doctrine_Record_SerializeUnserialize_TestCase
 *
 * @package     Doctrine
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Revision$
 */
class Doctrine_Record_SerializeUnserialize_TestCase extends Doctrine_UnitTestCase 
{
    
    public function prepareTables()
    {
        $this->tables[] = 'SerializeTest';
        $this->tables[] = 'TestRecord';

        parent::prepareTables();
    }
    
    public function prepareData()
    { }

    public function testSerializeUnserialize()
    {
        $object = new SerializeTest();
        $object->booltest = true;
        $object->integertest = 13;
        $object->floattest = 0.13;
        $object->stringtest = 'string';
        $object->arraytest = array(1, 2, 3);
        $object->objecttest = new TestObject(13);
        $object->enumtest = 'java';
        $object->blobtest = 'blobtest';
        $object->clobtest = 'clobtest';
        $object->gziptest = 'gziptest';
        $object->timestamptest = '2007-08-07 11:55:00';
        $object->timetest = '11:55:00';
        $object->datetest = '2007-08-07';
        
        $object->save();
        
        $object_before = clone($object);
        $serialized = serialize($object);
        $object_after = unserialize($serialized);
        
        $this->assertIdentical($object_before->booltest, $object_after->booltest);
        $this->assertIdentical($object_before->integertest, $object_after->integertest);
        $this->assertIdentical($object_before->floattest, $object_after->floattest);
        $this->assertIdentical($object_before->stringtest, $object_after->stringtest);
        $this->assertIdentical($object_before->arraytest, $object_after->arraytest);
        $this->assertIdentical($object_before->enumtest, $object_after->enumtest);
        $this->assertEqual($object_before->objecttest, $object_after->objecttest);
        $this->assertIdentical($object_before->blobtest, $object_after->blobtest);
        $this->assertIdentical($object_before->clobtest, $object_after->clobtest);
        $this->assertIdentical($object_before->gziptest, $object_after->gziptest);
        $this->assertIdentical($object_before->timestamptest, $object_after->timestamptest);
        $this->assertIdentical($object_before->timetest, $object_after->timetest);
        $this->assertIdentical($object_before->datetest, $object_after->datetest);
        
    }
    
    public function testSerializeUnserializeRecord()
    {
        $test = new TestRecord();
        $test->save();
        
        $object = new SerializeTest();
        $object->objecttest = $test;
         
        $object->save();
        
        $object_before = clone($object);
       
        $serialized = serialize($object);
        $object_after = unserialize($serialized);
        
        $this->assertIdentical(get_class($object_after->objecttest), 'TestRecord');
    }

    public function testUnserializeField()
    {
        $model = new SerializeTest();
        $table = $model->getTable();
        $cascadeValue = [
            'string' => 'просто строка',
            'array'  => [
                'sub_1' => false,
                'sub_2' => 'вложенная строка',
                'sub_3' => 123,
            ]
        ];

        /**
         * 1. Простой тест десериализации
         */
        $serializedCascade = serialize($cascadeValue);
        $preparedValue = $table->prepareValue('arraytest', $serializedCascade);
        $this->assertEqual($cascadeValue, $preparedValue);

        /**
         * 2. Извне меняем строку в уже сериализованном блобе
         */
        $serializedCascade = serialize($cascadeValue);
        $corruptedCascade = str_replace('просто строка', 'простая и длинная строка', $serializedCascade);
        $corruptedCascade = str_replace('вложенная строка', 'вложенная и длинная строка', $corruptedCascade);
        $preparedValue = $table->prepareValue('arraytest', $corruptedCascade);

        // на выходе должно получиться
        $newCascadeValue = [
            'string' => 'простая и длинная строка',
            'array'  => [
                'sub_1' => false,
                'sub_2' => 'вложенная и длинная строка',
                'sub_3' => 123,
            ]
        ];
        $this->assertEqual($newCascadeValue, $preparedValue);

        /**
         * 3. Тест для Таймпада, с двойной сериализацией
         */
        $serializedCascade = serialize($cascadeValue); // это происходит в модели
        $twiceSerializedCascade = serialize($serializedCascade); // это - в Record

        // изменяем строки в дважды сериализованном массиве
        $corruptedCascade = str_replace('просто строка', 'простая и длинная строка', $twiceSerializedCascade);
        $corruptedCascade = str_replace('вложенная строка', 'вложенная и длинная строка', $corruptedCascade);
        $preparedValue = $table->prepareValue('arraytest', $corruptedCascade);

        // на выходе должно получиться
        $twicePreparedValue = unserialize($preparedValue);
        $newCascadeValue = [
            'string' => 'простая и длинная строка',
            'array'  => [
                'sub_1' => false,
                'sub_2' => 'вложенная и длинная строка',
                'sub_3' => 123,
            ]
        ];
        $this->assertEqual($newCascadeValue, $twicePreparedValue);
    }
}

class TestObject
{
    
    private $test_field;
    
    public function __construct($value)
    {
        $this->test_field = $value;
    }
        
}

