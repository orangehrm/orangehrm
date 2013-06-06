 
        <script type="text/javascript">

            $(document).ready(function() {                            
                
                /* Enabling tooltips */
                $(".tiptip").tipTip();

                /* Toggling header menus */
                $("#welcome").click(function () {
                    $("#welcome-menu").slideToggle("fast");
                    $(this).toggleClass("activated-welcome");
                    return false;
                });
                
                $("#help").click(function () {
                    $("#help-menu").slideToggle("fast");
                    $(this).toggleClass("activated-help");
                    return false;
                });
                
                $('.panelTrigger').outside('click', function() {
                    $('.panelContainer').stop(true, true).slideUp('fast');
                });                

                /* 
                 * Button hovering effects 
                 * Note: we are not using pure css using :hover because :hover applies to even disabled elements.
                 * The pseudo class :enabled is not supported in IE < 9.
                 */                
                $(document).on({
                    mouseenter: function () {
                        $(this).addClass('hover');                        
                    },
                    mouseleave: function () {
                        $(this).removeClass('hover');                        
                    }

                }, 'input[type=button], input[type=submit], input[type=reset]'); 
  
                /* Fading out main messages */
                $(document).on({
                    click: function() {
                        $(this).parent('div.message').fadeOut("slow");
                    }
                }, '.message a.messageCloseButton');                

                /* Toggling search form: Begins */
                //$(".toggableForm .inner").hide(); // Disabling this makes search forms to be expanded by default.

                $(".toggableForm .toggle").click(function () {
                    $(".toggableForm .inner").slideToggle('slow', function() {
                        if($(this).is(':hidden')) {
                            $('.toggableForm .tiptip').tipTip({content:'<?php echo __(CommonMessages::EXPAND_OPTIONS); ?>'});
                        } else {
                            $('.toggableForm .tiptip').tipTip({content:'<?php echo __(CommonMessages::HIDE_OPTIONS); ?>'});
                        }
                    });
                    $(this).toggleClass("activated");
                });
                /* Toggling search form: Ends */

                /* Enabling/disabling form fields: Begin */
                
                $('form.clickToEditForm input, form.clickToEditForm select, form.clickToEditForm textarea').attr('disabled', 'disabled');
                $('form.clickToEditForm input.calendar').datepicker('disable');
                $('form.clickToEditForm input[type=button]').removeAttr('disabled');
                
                $('form input.editButton').click(function(){
                    $('form.clickToEditForm input, form.clickToEditForm select, form.clickToEditForm textarea').removeAttr('disabled');
                    $('form.clickToEditForm input.calendar').datepicker('enable');
                });
                
                /* Enabling/disabling form fields: End */
                
            });
            
        </script>        

    </body>
    
</html>

