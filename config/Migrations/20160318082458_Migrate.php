<?php
use Migrations\AbstractMigration;

class Migrate extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('tasks', ['id' => false, 'primary_key' => ['id']]);
        
        $table->addColumn('tID', 'integer', [
            'autoIncrement' => true,
            'limit' => 20
        ]);
        $table->addColumn('exec_time', 'datetime', [
            'default' => new \DateTime('now'),
            'null' => false
        ]);
        $table->addColumn('status', 'integer', [
            'default' => 0,
            'length' => 3
        ]);
        $table->addColumn('comment', 'string', [
            'default' => '',
            'length' => 255
        ]);
        $table->addColumn('error', 'string', [
            'default' => '',
            'length' => 255
        ]);
        $table->addColumn('config', 'string', [
            'null' => false,
            'length' => 255
        ]);
        $table->addPrimaryKey('tID');
        
        
        $table = $this->table('subtasks', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('sID', 'integer', [
            'autoIncrement' => true,
            'limit' => 25
        ]);
        $table->addColumn('tID', 'integer', [
            'autoIncrement' => true,
            'limit' => 20
        ]);
        $table->addColumn('status', 'integer', [
            'default' => 0,
            'length' => 3
        ]);
        $table->addColumn('file_path', 'string', [
            'default' => '',
            'length' => 255
        ]);
        $table->addColumn('error', 'string', [
            'default' => '',
            'length' => 255
        ]);
        $table->addPrimaryKey('sID');
    }
}
