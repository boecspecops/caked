<?php
    echo $this->Form->create();
    if($this->get('auth_url') === null) {
        echo $this->Form->input('Appname');
        echo $this->Form->input('Key');
        echo $this->Form->input('Secret');
        echo $this->Form->button(__('Get token'));
    } else if($this->get('token') === null){
        
        echo "1. Go to: " . $this->get('auth_url') . "<br>";
        echo "2. Click \"Allow\" (you might have to log in first).<br>";
        echo "3. Copy the authorization code.<br>";
        echo $this->Form->input('AuthCode');
        echo $this->Form->button(__('Auth me'));
    } else {
        echo "Access token: ". $this->get('token');
    }
    echo $this->Form->end();
?>
