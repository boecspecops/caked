<?php
namespace App\Test\TestCase\Model\Table;

use CakeD\Model\Table\TaskStatusTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TaskStatusTable Test Case
 */
class TaskStatusTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\TaskStatusTable
     */
    public $TaskStatus;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.task_status'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('TaskStatus') ? [] : ['className' => 'App\Model\Table\TaskStatusTable'];
        $this->TaskStatus = TableRegistry::get('TaskStatus', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TaskStatus);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
