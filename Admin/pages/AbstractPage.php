<?php

namespace Another_Twitter_Plugin\Admin\pages;

class AbstractPage {
            
    public static function render()
    {
        return new static();
    }
    
}
