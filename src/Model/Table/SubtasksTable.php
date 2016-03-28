<?php
namespace App\Model\Table;

use App\Model\Entity\Subtask;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Subtasks Model
 *
 */
class SubtasksTable extends Table
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

        $this->table('subtasks');
        $this->displayField('sID');
        $this->primaryKey('sID');
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
            ->integer('sID')
            ->allowEmpty('sID', 'create');

        $validator
            ->integer('tID')
            ->requirePresence('tID', 'create')
            ->notEmpty('tID');

        $validator
            ->integer('status')
            ->allowEmpty('status');

        $validator
            ->allowEmpty('file_path');

        $validator
            ->allowEmpty('comment');

        $validator
            ->allowEmpty('error');

        return $validator;
    }
}
