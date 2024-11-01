var $jx = jQuery.noConflict();

$jx(document).ready(function(){    
    $jx('form[name="sendy"]').live('submit', function(){
        $fname = $jx('input[name="fname"]').val();
        $email = $jx('input[name="email"]').val();
        $lname = ($jx('input[name="lname"]').val()) ? $jx('input[name="lname"]').val() : '';
        $jx('.sendy-message').slideUp('fast');
        $jx(this).find('input[type="submit"]').attr('disabled', 'disabled').addClass('disabled');


        $jx.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: "json",
            cache: false,
            crossDomain: true,
            data: { 
                action : 'sendy_subscribe',
                fname: $fname,
                lname: $lname,
                email: $email,
                guid: $jx('input[name="guid"]').val()
            },
            success: function($result) {
                console.log($result);
                if ($result.success) {
                    $jx('.sendy-message').html($result.message);
                    $jx('input[name="name"]').val("");
                    $jx('input[name="email"]').val("");
                }
                else {
                    $jx('.sendy-message').html($result.message);                    
                }
                $jx('.sendy-message').slideDown('slow');
                $jx('form[name="sendy"] input[type="submit"]').removeAttr('disabled').removeClass('disabled');
            }
        });         
        return false;
    });
});