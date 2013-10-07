$(document).ready(function()
{
	$("#address1").blur(function(){
        var alias = jQuery("#alias");
        var address1 = jQuery("#address1").val();
        //if(alias.val() == "")
            alias.attr("value", address1);
    });
    
    jQuery("#button_add_product").click(function(){
        alert("entra producto");
    });
    /*Add quantity*/
    jQuery("#add_quantity_link, #delete_quantity_link").click(function(event){
        if (jQuery("#quantity_add_delete").val().length < 1 || jQuery("#quantity_add_delete").val() <= 0 ) {            
            return false;
        }
        
        var quantity_add = jQuery("#quantity_add_delete").val();
        var url = $(this).attr('href'); 
        //alert(quantity_add);
        var new_url = url+"&quantity_1="+quantity_add;
        //alert(param.attr());
        var url = new_url;
        $(this).attr('href', new_url);
        
    });

    /*Delete quantity*/
    /*jQuery("#delete_quantity_link").click(function(){
        if (jQuery("#quantity_add_delete").val().length<1) {
            var quantity_val = 1;            
        }else{
            var quantity_val = jQuery("#quantity_add_delete").val();
        }        
        var quantity_delete = quantity_val;
        var url = $(this).attr('href'); 
        alert(quantity_delete);
        var new_url = url+"&quantity_1="+quantity_delete;
        //alert(param.attr());
        var url = new_url;
        $(this).attr('href', new_url);
        console.log(new_url);
    });
*/
    $("#quantity_add_delete").keydown(function(event){
        if(event.shiftKey)
        {
            event.preventDefault();
        }

        if (event.keyCode == 46 || event.keyCode == 8)    {
        }
        else {
            if (event.keyCode < 95) {
              if (event.keyCode < 49 || event.keyCode > 57) {
                event.preventDefault();
            }
        } 
        else {
          if (event.keyCode < 96 || event.keyCode > 105) {
              event.preventDefault();
          }
        }
        }
    });

    jQuery("#menu_header_bottom .header-link-bottom, ").hover(function(e) {    	
    	jQuery(this).find(".menu-dropdown").css('display', 'block');
    	e.preventDefault();
    }, function(e) {
    	jQuery(this).find(".menu-dropdown").css('display', 'none');
    	e.preventDefault();
    });
});

