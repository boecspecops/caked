<?php

namespace CakeD\View\Helper;

use CakeD\Core\Task;


class RemoteLinkHelper extends Helper {
    public $helpers = ['Html', 'Url'];
    
    public function css($file) {
        Task::getUrlBase($this->Url->css($file));
    }
    
    public function script($file) {
        Task::getUrlBase($this->Url->js($file));
    }
    
    public function image($file) {
        Task::getUrlBase($this->Url->img($file));
    }
}
