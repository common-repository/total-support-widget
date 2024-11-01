var $jx = jQuery.noConflict();
var $active_tab;
$jx(document).ready(function(){
    $jx('.jquery-ui-tabs').tabs({active: 0});
    
    $jx('input[name="savewidget"]').live('click', function(){
        $jx(this).parents('form').find('.spinner').addClass('saving');
        $active2 = $jx(this).parents('form').find('.jquery-ui-tabs li.ui-state-active').data('tab');
        
        myTimer = setInterval(function(){            
            if (!$jx('.saving').hasClass('is-active')) {
                $jx('.jquery-ui-tabs').tabs({active: $active2 - 1});
                $jx(".jquery-ui-tabs").tabs("refresh");
                $jx('.psw-color-picker').wpColorPicker();
                $jx('.saving').removeClass('saving');
                clearInterval(myTimer);
            }
        }, 50);        
    });
    
    $jx('.am-newletter').live('change', function(){
        $val = $jx(this).val();
        $jx('.active-newsletter').removeClass('active-newsletter');
        $jx('.info-' + $val).addClass('active-newsletter');
    });      
    
    $jx('.psw-color-picker').wpColorPicker();
    
    $jx( "#slider" ).slider({
            value:100,
            min: 0,
            max: 500,
            step: 50,
            slide: function( event, ui ) {
                    $jx( "#amount" ).val( "$" + ui.value );
            }
    });
    $jx( "#amount" ).val( "$" + $jx( "#slider" ).slider( "value" ) );    
});