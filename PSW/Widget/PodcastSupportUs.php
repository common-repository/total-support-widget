<?php
class PSW_Widget_PodcasrSupportUs extends WP_Widget{
    private $_fields1 = array('patreon-img', 'sendy-url', 'sendy-key', 'sendy-list', 'success', 'bottomtext', 'guid', 'patreon-show-img', 'patreon-show-link', 'mailchimp-key', 'mailchimp-list', 'newsletter', 'enable-support-link', 'color-font-awesome', 'size-font-awesome', 'color-bg', 'color-bg-border', 'color-text', 'color-btn-bg', 'color-btn', 'color-textbox-bg', 'color-textbox', 'color-textbox-border', 'disable-top-image');
    
    private $_fields2 = array('android', 'apple', 'behance', 'btc', 'facebook', 'flickr', 'github', 'google-plus', 'home', 'instagram', 'linkedin', 'medium', 'pinterest', 'rss', 'soundcloud', 'trello', 'twitch', 'tumblr', 'twitter', 'youtube');
        
    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
        parent::__construct('total-support-widget', // Base ID
			'Total Support Widget', // Name
			array( 'description' => 'The Total Support Widget gives you Patreon, 20 social & support links, and Sendy or Mailchimp signup.') // Args);
        );        
    }
    
    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        $input_fname = '';
        $input_lname = '';
        $input_email = '';

        if (filter_input(INPUT_POST, 'guid')) {
            $ret = $this->sendySubscribe();
            $input_fname = $_POST['fname'];
            $input_lname = $_POST['lname'];
            $input_email = $_POST['email'];
        }        
        
        ?>
        <?php if ($instance['disable-top-image'] != 'on') { ?>
        <div class="am-socials-top">
            <a class="patreon-show" target="_blank" href="<?php echo $instance['patreon-show-link'] ?>"><img alt="Patreon Show" src="<?php echo $instance['patreon-show-img'] ?>" /></a>
        </div>
        <?php } ?>
        <div class="am-socials-main">
            <div class="socials">                
        <?php
            $ctr = 1;
            foreach ($this->_fields2 as $idx) {
                $link = $instance[$idx];
                if ($link) {
                    echo '<span class="wrap"><a class="icon-' . $idx . ' social-icon" target="_blank" href="' . $link . '"><i class="fa fa-' . $idx . '"></i></a></span>';
                    $ctr++;
                    if ($ctr % 5 == 1) {
                        echo '<span class="break"></span>';
                    }                      
                }              
            }
        ?>
            </div>
            <div class="newsletter">
                <?php
                if ($instance['newsletter'] != 'none') {
                ?>                
                <div class="envelope-icon"></div>
                <h3 class="join"><a name="am-submit">Join Our Newsletter</a></h3>
                <div class="sendy-message" <?php echo (!isset($ret['message'])) ? 'style="display: none"' :'' ?>><?php echo $ret['message']; ?></div>                
                <form name="sendy" class="newsletter-subscribe" method="POST" action="#am-submit">
                    <?php if ($instance['newsletter'] == 'mailchimp') { ?>
                        <div class="line"><input type="text" name="fname" placeholder="First Name" value="<?php echo $input_fname ?>" /></div>
                        <div class="line"><input type="text" name="lname" placeholder="Last Name" value="<?php echo $input_lname ?>" /></div>
                    <?php }
                    else {
                        ?>
                        <div class="line"><input type="text" name="fname" placeholder="Name" value="<?php echo $input_fname ?>" /></div>
                        <?php
                    }?>
                    <div class="line"><input type="email" name="email" placeholder="Email" value="<?php echo $input_email ?>" /></div>
                    <input type="hidden" name="guid" value="<?php echo $instance['guid'] ?>" />
                    <div class="line"><input type="submit" value="Subscribe" /></div>
                </form>                
                <?php } ?>
                <div class="bottom-text"><?php echo $instance['bottomtext'] ?></div>
                <?php if ($instance['enable-support-link'] == 'on') { ?>
                <p class="plugin-copyright">Widget by <a href="http://ampodcastnetwork.com/" target="_blank">AM Podcast Network</a></p>
                <?php } ?>
            </div>
            
        </div> 
        <style type="text/css">
            #<?php echo $args['widget_id'] ?> .social-icon {
                color: <?php echo isset($instance['color-font-awesome']) ? $instance['color-font-awesome'] : 'red'?>;
                <?php echo isset($instance['size-font-awesome']) ? 'font-size: ' . $instance['size-font-awesome'] . 'px;' : ''?>
            }
            #<?php echo $args['widget_id'] ?> .am-socials-main {
                <?php echo isset($instance['color-bg']) ? 'background-color: ' . $instance['color-bg'] . ';' : ''?>
            }
            #<?php echo $args['widget_id'] ?> .join a, #<?php echo $args['widget_id'] ?> .bottom-text {
                <?php echo isset($instance['color-text']) ? 'color: ' . $instance['color-text'] . ';' : ''?>
            }            
            #<?php echo $args['widget_id'] ?> .newsletter-subscribe input[type="submit"] {
                <?php echo isset($instance['color-btn-bg']) ? 'background-color: ' . $instance['color-btn-bg'] . ';' : ''?>
                <?php echo isset($instance['color-btn']) ? 'color: ' . $instance['color-btn'] . ';' : ''?>
            }
            #<?php echo $args['widget_id'] ?> .newsletter-subscribe input[type="text"],
            #<?php echo $args['widget_id'] ?> .newsletter-subscribe input[type="email"] {
                <?php echo isset($instance['color-textbox']) ? 'color: ' . $instance['color-textbox'] . '!important;' : ''?>
                <?php echo (isset($instance['color-textbox-border']) && $instance['color-textbox-border'] != '') ? 'border: solid ' . $instance['color-textbox-border'] . ' 1px;' : ''?>
            }
            #<?php echo $args['widget_id'] ?> .newsletter-subscribe input::-webkit-input-placeholder {
                <?php echo isset($instance['color-textbox']) ? 'color: ' . $instance['color-textbox'] . '!important;' : ''?>
            }
            #<?php echo $args['widget_id'] ?> .newsletter-subscribe input:-moz-placeholder { /* Firefox 18- */
                <?php echo isset($instance['color-textbox']) ? 'color: ' . $instance['color-textbox'] . '!important;' : ''?>
            }
            #<?php echo $args['widget_id'] ?> .newsletter-subscribe input::-moz-placeholder {  /* Firefox 19+ */
                <?php echo isset($instance['color-textbox']) ? 'color: ' . $instance['color-textbox'] . '!important;' : ''?>
            }
            #<?php echo $args['widget_id'] ?> .newsletter-subscribe input:-ms-input-placeholder {  
                <?php echo isset($instance['color-textbox']) ? 'color: ' . $instance['color-textbox'] . '!important;' : ''?>
            }            
        </style>
        <?php
        echo $args['after_widget'];
    }

    private function _getFormFieldPassword($idx, $label, $value) {
        ?>
            <p>
            <label for="<?php echo $this->get_field_id($idx); ?>"><?php echo $label; ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id($idx); ?>" name="<?php echo $this->get_field_name($idx); ?>" type="password" value="<?php echo esc_attr($value); ?>">
            </p>	
        <?php
    }
    
    private function _getFormField($idx, $label, $value) {
        ?>
            <p class="field-<?php echo $idx ?>">
            <label for="<?php echo $this->get_field_id($idx); ?>"><?php echo $label; ?></label> 
            <input id="<?php echo $this->get_field_id($idx); ?>" name="<?php echo $this->get_field_name($idx); ?>" type="text" value="<?php echo esc_attr($value); ?>">
            </p>	
        <?php
    }
    private function _getFormCheckboxField($idx, $label, $value) {
        ?>
            <p>
            <input class="widefat" id="<?php echo $this->get_field_id($idx); ?>" name="<?php echo $this->get_field_name($idx); ?>" type="checkbox" <?php echo (esc_attr($value) == 'on') ? 'checked="checked"' : ''; ?> />
            <label for="<?php echo $this->get_field_id($idx); ?>"><?php echo $label; ?></label> 
            </p>	
        <?php
    }    
    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
        $guid = ! empty( $instance['guid'] ) ? $instance['guid'] : uniqid('am-');
        
        $form = new PSW_Form_PodcastSupportUs();
        $fields = array_merge($this->_fields1, $this->_fields2);
        foreach ($fields as $idx) {
            $data[$idx] = array('name' => $this->get_field_name($idx), 'id' => $this->get_field_id($idx), 'value' => $instance[$idx]);
        }
        
        
        $form->setValues($data);
        $form->showFormElements();
        ?>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        foreach ($this->_fields1 as $idx1) {
            $instance[$idx1] = ( ! empty( $new_instance[$idx1] ) ) ? strip_tags( $new_instance[$idx1] ) : '';
        }
        foreach ($this->_fields2 as $idx1) {
            $instance[$idx1] = ( ! empty( $new_instance[$idx1] ) ) ? strip_tags( $new_instance[$idx1] ) : '';
        }        

        $guid = $new_instance['guid'];        
        $am_socials = get_option('am-newsletter-options');
        
        $am_socials[$guid]['success'] = sanitize_text_field($new_instance['success']);
        $am_socials[$guid]['type'] = sanitize_text_field($new_instance['newsletter']);
        $am_socials[$guid]['sendy']['url'] = sanitize_text_field($new_instance['sendy-url']);
        $am_socials[$guid]['sendy']['key'] = sanitize_text_field($new_instance['sendy-key']);
        $am_socials[$guid]['sendy']['list'] = sanitize_text_field($new_instance['sendy-list']);

        $am_socials[$guid]['mailchimp']['key'] = sanitize_text_field($new_instance['mailchimp-key']);
        $am_socials[$guid]['mailchimp']['list'] = sanitize_text_field($new_instance['mailchimp-list']);
        $am_socials[$guid]['disable-support-link'] = sanitize_text_field($new_instance['disable-support-link']);
        
        update_option('am-newsletter-options', $am_socials);


        return $instance;
    }    
}