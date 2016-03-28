<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FileStatusFixture
 *
 */
class FileStatusFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'file_status';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'fsID' => ['type' => 'integer', 'length' => 3, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'description' => ['type' => 'string', 'length' => 128, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['fsID'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'latin1_swedish_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'fsID' => 1,
            'description' => 'Lorem ipsum dolor sit amet'
        ],
    ];
}
