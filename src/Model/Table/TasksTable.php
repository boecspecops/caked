<?php
namespace App\Model\Table;

use App\Model\Entity\Task;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tasks Model
 *
 */
class TasksTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('tasks');
        $this->displayField('tID');
        $this->primaryKey('tID');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('tID')
            ->allowEmpty('tID', 'create');

        $validator
            ->dateTime('exec_time')
            ->requirePresence('exec_time', 'create')
            ->notEmpty('exec_time');

        $validator
            ->integer('status')
            ->allowEmpty('status');

        $validator
            ->allowEmpty('config_file');

        $validator
            ->allowEmpty('comment');

        $validator
            ->allowEmpty('error');

        return $validator;
    }
}
