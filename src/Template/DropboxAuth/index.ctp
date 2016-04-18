<?php
    echo $this->Form->create($article);
    echo $this->Form->input('Key');
    echo $this->Form->input('Secret', ['rows' => '3']);
    echo $this->Form->button(__('Get token'));
    echo $this->Form->end();
?>
