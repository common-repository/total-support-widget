<?php
abstract class PSW_Form {
    protected $_id; //form id
    protected $_location; //wp or admin
    protected static $_plugin_folder;
    protected $_data;
    protected $_dbid;
    protected $_prefix;
    
    public function __construct($class_name, $location) {
        $this->_id              = strtolower(str_replace('_', '-', $class_name));
        $this->_location            = $location;
        $this->_prefix = strtolower(basename(dirname(__FILE__)));
        if ($location == 'wp') {
            add_action('wp_footer', array($this, 'addScript'));        
        }
        else {
            add_action('admin_footer', array($this, 'addScript'));        
        }
    }
    
    public function setValues($data) {
        $this->_data = $data;
    }

    public function setDBID($id) {
        $this->_dbid = $id;
    }

    public static function init($plugin_folder) {
        $class = get_class();
        $class::$_plugin_folder = $plugin_folder;
        add_action('wp_ajax_save_ajax_form_' . $plugin_folder, array($class, 'ajaxCallSaveForm'));
        add_action('wp_ajax_nopriv_save_ajax_form' . $plugin_folder, array($class, 'ajaxCallSaveForm'));        
    }    
     
    public static function ajaxCallSaveForm() {
//        header('content-type: application/json; charset=utf-8');
//        header("access-control-allow-origin: *");        
//        echo $_GET['callback'] . '(' . json_encode($ret) . ')';
        $data1 = $_POST['data'];
        if (isset($_POST['dbid'])) {
            $data1['ID'] = $_POST['dbid'];
        }
        $class_name = get_class();
        $class = $class_name::unswapChars($_POST['key_cn']);
        //$obj = new $class($_POST['key_id']);
        $data = $class::saveForm($data1);
        print_r( json_encode($data));
        exit;
    }
     
    public function showForm() {
    ?>
    <form class="od-form-<?php echo $this->_id ?>" name="form-<?php echo $this->_id ?>" method="post">
        <?php
        if ($this->_dbid) {
            echo '<input type="hidden" name="dbid" value="' . $this->_dbid . '" />';
        }
        ?>        
        <?php $this->showFormElements() ?>
    </form>
    <?php
    }
     
    public static function swapChars($text) {
        $chars1 = 'praywithoutceasing';
        $chars2 = '12345678907!@3#6$%';
         
        for ($i = 0; $i < strlen($text); $i++) {
            $pos = strpos($chars1, $text[$i]);
            if ($pos !== false) {
                $text[$i] = $chars2[$pos];
            }
        }
         
         
        return $text;
    }
     
    public static function unSwapChars($text) {
        $chars1 = '12345678907!@3#6$%';
        $chars2 = 'praywithoutceasing';
         
        for ($i = 0; $i < strlen($text); $i++) {
            $pos = strpos($chars1, $text[$i]);
            if ($pos !== false) {
                $text[$i] = $chars2[$pos];
            }
        }
         
         
        return $text;
    }    
    
    public function getDropDownField($select_name, $select_value, $options = array()) {
        $opt = '<select name="' . $select_name . '">';

        foreach ($options as $idx => $value) {
            $is_selected = ($idx == $select_value) ? 'selected="selected"': '';
            $opt .= '<option value="' . $idx . '" ' . $is_selected . '>' . $value . '</option>';
        }

        $opt .= '</select>';
        return $opt;
    }
    
    public function _showDropDownField($label, $fieldname, $args = array()) {
        $options = array_merge(array(), $args);
        $opt = '<select name="data[' . $fieldname . ']">';

        $db_fieldname = str_replace('-', '_', $fieldname);
        $value = ($this->_data->$db_fieldname) ? $this->_data->$db_fieldname : (($args['default']) ? $args['default'] : '');
        
        foreach ($args['options'] as $idx => $text) {
            $is_selected = ($idx == $value) ? 'selected="selected"': '';
            //echo "<br />Value = " . $value . ' - ' . $idx;
            $opt .= '<option value="' . $idx . '" ' . $is_selected . '>' . $text . '</option>';
        }

        $opt .= '</select>';
        
        $html = '<div class="std-form-line">
                    <label>' . $label . '</label>
                    <div class="grp">
                        ' . $opt . '
                        <div>' . (($options['note1']) ? '<span class="important-note">' . $options['note1'] . '</span>' : '') . '</div>
                    </div>
                </div>';
        
        echo $html;
    }
    
    protected function _showStandardDatePicker($label, $fieldname, $args = array()) {
        $options = array_merge(array(), $args);

        $db_fieldname = str_replace('-', '_', $fieldname);
        $value = ($this->_data->$db_fieldname) ? $this->_data->$db_fieldname : (($args['default']) ? $args['default'] : '');
        
        $html = '<div class="std-form-line">
                    <label>' . $label . '</label>
                    <div class="grp">
                        <input class="date-picker" type="text" ' . (($options['placeholder']) ? 'placeholder="' . $options['placeholder'] . '"' : '') . ' name="data[' . $fieldname . ']" style="height: ' . $options['height'] . '" value="' . $value . '" />
                        <div>' . (($options['note1']) ? '<span class="important-note">' . $options['note1'] . '</span>' : '') . '</div>
                    </div>
                </div>';
        echo $html;
    }    
    
 
    
    public function _showStandardTextAreaField($label, $fieldname, $args = array()) {
        $options = array_merge(array(
            'height' => 'auto',
            'note1' => '',
            ), $args);

        $db_fieldname = str_replace('-', '_', $fieldname);
        $value = ($this->_data->$db_fieldname) ? $this->_data->$db_fieldname : (($args['default']) ? $args['default'] : '');
        
        $html = '<div class="std-form-line">
            <label>' . $label . '</label>
            <div class="grp">
                <textarea name="data[' . $fieldname . ']" style="height: ' . $options['height'] . '">' . stripslashes($value) . '</textarea>
                <div>' . (($options['note1']) ? '<span class="important-note">' . $options['note1'] . '</span>' : '') . '</div>
            </div>
        </div> ';
        return $html;
    }
    

    public function _showWPEditorField($label, $fieldname, $args = array()) {
        $options = array_merge(array(
            'height' => 'auto',
            'note1' => '',
            ), $args);

        $db_fieldname = str_replace('-', '_', $fieldname);
        $value = ($this->_data->$db_fieldname) ? $this->_data->$db_fieldname : (($args['default']) ? $args['default'] : '');
        
        $html1 = '<div class="std-form-line">
            <label>' . $label . '</label>
            <div class="grp">';
        echo $html1;
        wp_editor($value, $fieldname, array('editor_height' => '80px', 'media_buttons' => true));
        $html2 = '<div>' . (($options['note1']) ? '<span class="important-note">' . $options['note1'] . '</span>' : '') . '</div>
            </div>
        </div> ';
        echo $html2;
    }
    
    /**
     * 
     * @param type $label
     * @param array $args
     */
    protected function _showColorPickerField($label, $args = array()) {
        $options = array_merge(array(
            'name'      => '',
            'id'        => '',
            'note1'     => '',
            ), $args);

        //$db_fieldname = str_replace('-', '_', $fieldname);                
        //$value = ($this->_data->$db_fieldname) ? $this->_data->$db_fieldname : (($args['default']) ? $args['default'] : '');
        
        $fieldname = $options['name'];        
        $value = $options['value'];        
        $field_id = (($options['id']) ? $options['id'] : $fieldname);

        $html = '<div class="std-form-line type-color-picker">
            <label for="' . $field_id . '">' . $label . '</label>
            <div class="grp">
                <input class="' . $this->_prefix . '-color-picker" id="' . $field_id . '" name="' . $fieldname . '" type="text" value="' . $value . '" />
                <div>' . (($options['note1']) ? '<span class="important-note">' . $options['note1'] . '</span>' : '') . '</div>
                </div>
            </div>';
        echo $html;
    }          
    
    /**
     * 
     * @param type $label
     * @param array $args
     */
    protected function _showMediaUploaderField($label, $args = array()) {
        $options = array_merge(array(
            'name'      => '',
            'id'        => '',
            'note1'     => '',
            ), $args);

        //$db_fieldname = str_replace('-', '_', $fieldname);                
        //$value = ($this->_data->$db_fieldname) ? $this->_data->$db_fieldname : (($args['default']) ? $args['default'] : '');
        
        $fieldname = $options['name'];        
        $value = $options['value'];        
        $field_id = (($options['id']) ? $options['id'] : $fieldname);

        $html = '<div class="std-form-line">
            <label for="' . $field_id . '">' . $label . '</label>
            <div class="grp">
                <div class="uploader">
                    <input id="' . $field_id . '" name="' . $fieldname . '" type="text" value="' . $value . '" />
                    <input class="button ' . strtolower(get_class()) . '-media-uploader" rel="' .  $field_id . '" type="button" value="Upload" />
                </div>
                <div>' . (($options['note1']) ? '<span class="important-note">' . $options['note1'] . '</span>' : '') . '</div>
                </div>
            </div>';
        echo $html;
    }        
    
    /**
     * 
     * @param type $label
     * @param array $args
     */    
    protected function _showStandardTextField($label, $args = array()) {
        $options = array_merge(array(            
            'id'            => '',
            'name'          => '',
            'value'         => '',
            'default'       => '',
            'placeholder'   => '',
            'note1'         => '',
            ), $args);

        //$db_fieldname = str_replace('-', '_', $fieldname);
        //$value = ($this->_data->$db_fieldname) ? $this->_data->$db_fieldname : (($args['default']) ? $args['default'] : '');
        
        $fieldname = $options['name'];        
        $value = ($options['value']) ? $options['value'] : $options['default'];
        $field_id = (($options['id']) ? $options['id'] : $fieldname);
        
        $html = '<div class="std-form-line">
                    <label for="' . $field_id . '">' . $label . '</label>
                    <div class="grp">
                        <input class="input" id="' . $field_id . '" type="text" ' . (($options['placeholder']) ? 'placeholder="' . $options['placeholder'] . '"' : '') . ' name="' . $fieldname . '" value="' . $value . '" />
                        <div>' . (($options['note1']) ? '<span class="important-note">' . $options['note1'] . '</span>' : '') . '</div>
                    </div>
                </div>';
        echo $html;
    }    
    
    /**
     * 
     * @param type $label
     * @param array $args
     */    
    protected function _showStandardPasswordField($label, $args = array()) {
        $options = array_merge(array(            
            'id'            => '',
            'name'          => '',
            'value'         => '',
            'default'       => '',
            'placeholder'   => '',
            'note1'         => '',
            ), $args);

        //$db_fieldname = str_replace('-', '_', $fieldname);
        //$value = ($this->_data->$db_fieldname) ? $this->_data->$db_fieldname : (($args['default']) ? $args['default'] : '');
        
        $fieldname = $options['name'];        
        $value = ($options['value']) ? $options['value'] : $options['default'];
        $field_id = (($options['id']) ? $options['id'] : $fieldname);
        
        $html = '<div class="std-form-line">
                    <label for="' . $field_id . '">' . $label . '</label>
                    <div class="grp">
                        <input id="' . $field_id . '" type="password" ' . (($options['placeholder']) ? 'placeholder="' . $options['placeholder'] . '"' : '') . ' name="' . $fieldname . '" value="' . $value . '" />
                        <div>' . (($options['note1']) ? '<span class="important-note">' . $options['note1'] . '</span>' : '') . '</div>
                    </div>
                </div>';
        echo $html;
    } 
    
    protected function _showStandardCheckboxField($label, $args = array()) {
        $options = array_merge(array(            
            'id'            => '',
            'name'          => '',
            'value'         => '',
            'default'       => '',
            'placeholder'   => '',
            'note1'         => '',
            ), $args);
        
        //$db_fieldname = str_replace('-', '_', $fieldname);
        //$value = ($this->_data->$db_fieldname) ? $this->_data->$db_fieldname : (($args['default']) ? $args['default'] : '');
        
        $fieldname = $options['name'];        
        $value = ($options['value']) ? $options['value'] : $options['default'];
        $field_id = (($options['id']) ? $options['id'] : $fieldname);
        
        $html = '<div class="std-form-line">                    
                    <div class="grp">
                        <input type="checkbox" id="' . $field_id . '" name="' . $fieldname . '" ' . (($value == 1 || $value == 'on') ? 'checked="checked"' : '') . ' />
                        <label for="' . $field_id . '">' . $label . '</label>
                        <div>' . (($options['note1']) ? '<span class="important-note">' . $options['note1'] . '</span>' : '') . '</div>
                    </div>
                </div>';
        echo $html;
    }       
    /*public function addScript() {
?>
    <script type="text/javascript">
        var $jx = jQuery.noConflict();
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
 
        $jx('form[name="<?php echo 'form-' . $this->_id ?>"]').live('submit', function(){
            $form = $jx(this).serialize();
            $jx(this).find('input[type="submit"]').attr('disabled', 'disabled').addClass('sending-form');
            $jx('.od-form-msg').html('Saving...');

            $jx.ajax({
                url: ajaxurl, //AJAX file path – admin_url("admin-ajax.php")
                type: "POST",
                data: 'action=save_ajax_form_<?php echo $this->_plugin_folder ?>&' + $form + '&key_cn=<?php echo $this->swapChars(get_class($this)); ?>&key_id=<?php echo $this->_id ?>',
                dataType: "json",
                success: function($data){
                    if ($data.success) {
			$jx('.sending-form').removeAttr('disabled').removeClass('sending-form');
                        $jx('.od-form-msg').html($data.message);
                    }
                },
                error: function($data, $textStatus, $errorThrown){
                        $jx('.sending-form').removeAttr('disabled').removeClass('sending-form');
                        $jx('.od-form-msg').html('An error occured. Please try again.');
                }
            });
 
            return false;
        });
         
 
    </script>
<?php
    } */
    
    abstract public function showFormElements();
    abstract public static function saveForm($data);
} //end of class