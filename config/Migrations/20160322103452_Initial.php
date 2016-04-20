<?php
use Migrations\AbstractMigration;

class Initial extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('cake_d_subtasks', ['id' => false, 'primary_key' => ['sID']]);
        $table
            ->addColumn('sID', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 25,
                'null' => false,
            ])
            ->addColumn('tID', 'integer', [
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('status', 'integer', [
                'default' => 1,
                'limit' => 3,
                'null' => true,
            ])
            ->addColumn('file', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('comment', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('error', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $table = $this->table('cake_d_tasks', ['id' => false, 'primary_key' => ['tID']]);
        $table
            ->addColumn('tID', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('exec_time', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('status', 'integer', [
                'default' => 1,
                'limit' => 3,
                'null' => true,
            ])
            ->addColumn('config_file', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('comment', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('error', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

    }

    public function down()
    {
        $this->dropTable('subtasks');
        $this->dropTable('tasks');
    }
}
