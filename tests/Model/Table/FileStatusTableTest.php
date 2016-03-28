<?php
namespace App\Test\TestCase\Model\Table;

use CakeD\Model\Table\FileStatusTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FileStatusTable Test Case
 */
class FileStatusTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\FileStatusTable
     */
    public $FileStatus;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.file_status'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('FileStatus') ? [] : ['className' => 'App\Model\Table\FileStatusTable'];
        $this->FileStatus = TableRegistry::get('FileStatus', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->FileStatus);

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
