<?php
namespace App\Test\TestCase\Model\Table;

use CakeD\Model\Table\SubtaskTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SubtaskTable Test Case
 */
class SubtaskTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\SubtaskTable
     */
    public $Subtask;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.subtask'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Subtask') ? [] : ['className' => 'App\Model\Table\SubtaskTable'];
        $this->Subtask = TableRegistry::get('Subtask', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Subtask);

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
