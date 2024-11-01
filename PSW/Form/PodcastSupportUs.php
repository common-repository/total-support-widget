<?php

class PSW_Form_PodcastSupportUs extends PSW_Form {
    private $_socials = array('android' => 'Android', 'apple' => 'Apple', 'behance' => 'Behance', 'btc' => 'BTC', 'facebook' => 'Facebook', 'flickr' => 'Flickr', 'github' => 'Github', 'google-plus' => 'Google Plus', 'home' => 'Home', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'medium' => 'Medium', 'pinterest' => 'Pinterest', 'rss' => 'RSS', 'soundcloud' => 'SoundCloud', 'trello' => 'Trello', 'twitch' => 'Twitch', 'tumblr' => 'Tumbler', 'twitter' => 'Twitter', 'youtube' => 'Youtube');

    public function __construct() {
        $class_name = get_class();
        parent::__construct($class_name, 'wp');
    }
    public function showFormElements() { 
        include(ABSPATH . 'wp-content/plugins/total-support-widget/PSW/View/PodcastSupportUs.phtml');
    }

    public static function saveForm($data) {
        
    }

    
}