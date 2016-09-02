<?php
App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('DataStorageHelper', 'CakephpDataStorage.View/Helper');
App::uses('ClassRegistry', 'Utility');

class DataStorageHelperTest extends CakeTestCase
{
    public function setUp()
    {
        parent::setUp();
        $Controller = new Controller();
        $View = new View($Controller);
        $this->DataStorageHelper = new DataStorageHelper($View);
        $this->FormHelper = new FormHelper($View);

        // @todo To be change. Maybe by a mock object ?­(SL)
        ClassRegistry::addObject('Contact', new Contact());
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->DataStorageHelper);
        unset($this->FormHelper);
        // @todo Clear the ClassRegistry.
    }
}

