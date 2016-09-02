<?php
App::uses('ExtraCakeTestCase', 'TestSuite');
App::uses('StorableBehavior', 'cakephp-data-storage.Model/Behavior');

class StorableBehaviorTest extends ExtraCakeTestCase
{
    public $behavior = null;

    public function setUp()
    {
        parent::setUp();

        // Instantiate model.
        $this->Model = ClassRegistry::init('cakephp-data-storage.StorableBehavior');
        debug(($this->Model));
        die();
    }

    public function tearDown()
    {
        ClassRegistry::flush();
        unset($this->behavior);
        parent::tearDown();
    }


    public function testInstance()
    {
        $this->assertSame(true, $this->behavior instanceof StorableBehavior);
    }
}
