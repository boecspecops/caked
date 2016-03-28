<?php
namespace CakeD\Test\TestCase\Controller\Component;

use CakeD\Controller\Component\TaskManagerComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\TaskManagerComponent Test Case
 */
class TaskManagerComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Controller\Component\TaskManagerComponent
     */
    public $TaskManager;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->TaskManager = new TaskManagerComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TaskManager);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
