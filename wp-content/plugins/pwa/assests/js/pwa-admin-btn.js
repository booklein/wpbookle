// Jquery with no conflict
(function($){
	$(document).ready(function($) {
		
        //this function call with current button background color when page load
        changeButtonBackground($('#woocommerce_Pwacheckout_pwa_btn_bkgd').val());
        //this function call with current button color when page load
        changeButtonColorSize($('#woocommerce_Pwacheckout_pwa_btn_color').val(), $('#woocommerce_Pwacheckout_pwa_btn_size').val());   
        $( "#woocommerce_Pwacheckout_pwa_btn_color" ).on( "change", function() {    
            changeButtonColorSize($('#woocommerce_Pwacheckout_pwa_btn_color').val(), $('#woocommerce_Pwacheckout_pwa_btn_size').val());

        });
        $( "#woocommerce_Pwacheckout_pwa_btn_size" ).on( "change", function() {    
            changeButtonColorSize($('#woocommerce_Pwacheckout_pwa_btn_color').val(), $('#woocommerce_Pwacheckout_pwa_btn_size').val());
        });

        $( "#woocommerce_Pwacheckout_pwa_btn_bkgd" ).on( "change", function() {    
           changeButtonBackground($('#woocommerce_Pwacheckout_pwa_btn_bkgd').val())
        });

        // hide Choose a back ground colour for the button settinds from admin panel 
         $('#woocommerce_Pwacheckout_pwa_btn_bkgd').closest('tr').css({display :'none'});

        //show iopn dump file url 
        $( "input:checkbox[name=woocommerce_Pwacheckout_iopn_dump]" ).on("click",function() {   
               showHideIOPNDumpFilePath($('input:checkbox[name=woocommerce_Pwacheckout_iopn_dump]:checked').val());
          });
          showHideIOPNDumpFilePath($('input:checkbox[name=woocommerce_Pwacheckout_iopn_dump]:checked').val());

        //show mws order dump file url 
        $( "input:checkbox[name=woocommerce_Pwacheckout_mws_order_dump]" ).on("click",function() {   
               showHideMWSOrderDumpFilePath($('input:checkbox[name=woocommerce_Pwacheckout_mws_order_dump]:checked').val());
          });
          showHideMWSOrderDumpFilePath($('input:checkbox[name=woocommerce_Pwacheckout_mws_order_dump]:checked').val());

        //show mws report dump file url 
        $( "input:checkbox[name=woocommerce_Pwacheckout_mws_report_dump]" ).on("click",function() {   
            showHideMWSReportDumpFilePath($('input:checkbox[name=woocommerce_Pwacheckout_mws_report_dump]').attr('id'), $('input:checkbox[name=woocommerce_Pwacheckout_mws_report_dump]:checked').val());
        });
        showHideMWSReportDumpFilePath($('input:checkbox[name=woocommerce_Pwacheckout_mws_report_dump]').attr('id'),$('input:checkbox[name=woocommerce_Pwacheckout_mws_report_dump]:checked').val());

          //show mws report dump file url 
        $( "input:checkbox[name=woocommerce_Pwacheckout_mws_report_dump_url]" ).on("blur",function() {   
               showHideMWSReportDumpFilePath($('input:checkbox[name=woocommerce_Pwacheckout_mws_report_dump]').attr('id'), $('input:checkbox[name=woocommerce_Pwacheckout_mws_report_dump]:checked').val());
          });


        /*
        ** function checkStringEndEithSlash() is used to check that the file end with slash(/).
           If path not end with slash then it add slash with path to make proper directory path.
        */

        $( "#woocommerce_Pwacheckout_iopn_dump_url" ).blur(function() {
            checkStringEndWithSlash($("#woocommerce_Pwacheckout_iopn_dump_url").attr('id'));
        });

        $( "#woocommerce_Pwacheckout_mws_order_dump_url" ).blur(function() {
            checkStringEndWithSlash($("#woocommerce_Pwacheckout_mws_order_dump_url").attr('id'));
        });

        $( "#woocommerce_Pwacheckout_mws_report_dump_url" ).blur(function() {
            checkStringEndWithSlash($("#woocommerce_Pwacheckout_mws_report_dump_url").attr('id'));
        });

        $( "#woocommerce_Pwacheckout_mws_endpoint_url" ).blur(function() {
           isUrl($("#woocommerce_Pwacheckout_mws_endpoint_url").val());
        });

          //assign default value to url and make readonly filed
        $('#woocommerce_Pwacheckout_success_payment_return_url').val($('#pwa_urls').val()+"pwa_order");
        $('#woocommerce_Pwacheckout_success_payment_return_url').attr("readonly" ,"readonly");

        $('#woocommerce_Pwacheckout_iopn_merchant_url').val($('#pwa_iopn_urls').val()+"pwa_iopn ");
        $('#woocommerce_Pwacheckout_iopn_merchant_url').attr("readonly" ,"readonly");

        $('#woocommerce_Pwacheckout_mws_report_api_url').val($('#pwa_urls').val()+"pwa_mws_report");
        $('#woocommerce_Pwacheckout_mws_report_api_url').attr("readonly" ,"readonly");

        $('#woocommerce_Pwacheckout_mws_order_api_url').val($('#pwa_urls').val()+"pwa_mws");
        $('#woocommerce_Pwacheckout_mws_order_api_url').attr("readonly" ,"readonly");

        $('#woocommerce_Pwacheckout_mws_schedule_report_api_url').val($('#pwa_urls').val()+"pwa_mws_report_schedule");
        $('#woocommerce_Pwacheckout_mws_schedule_report_api_url').attr("readonly" ,"readonly");



        // change market place id and environment on change vice-versa
        $( "#woocommerce_Pwacheckout_marketplace_id" ).on( "change", function() {    
            if( $( "#woocommerce_Pwacheckout_marketplace_id" ).val() =="A3PY9OQTG31F3H" ){
              $( "#woocommerce_Pwacheckout_environment" ).val("prod");
            }
            else{
              $( "#woocommerce_Pwacheckout_environment" ).val("sandbox");
            }

        });
        $( "#woocommerce_Pwacheckout_environment" ).on( "change", function() {    
            if( $( "#woocommerce_Pwacheckout_environment" ).val() =="prod" ){
              $( "#woocommerce_Pwacheckout_marketplace_id" ).val("A3PY9OQTG31F3H");
            }
            else{
              $( "#woocommerce_Pwacheckout_marketplace_id" ).val("AXGTNDD750VEM");
            }

        });

        function changeButtonBackground(buttonbkgd){ 

            if ( buttonbkgd == "white" ) {
                $('#div_pwa_btn_admin').css({background : 'white'});
            }
            else {
                $('#div_pwa_btn_admin').css({background : '#5A7DC8'});
            }
        }

        function changeButtonColorSize(buttoncolor,buttonsize){
            $('#view_pwa_btn_admin').attr('src','https://paywithamazon.amazon.in/gp/cba/button?size='+buttonsize+'&color='+buttoncolor+'&background=white');     
        }


        function showHideIOPNDumpFilePath(iopn){
           if(iopn ==1){
                $('#woocommerce_Pwacheckout_iopn_dump_url').closest('tr').css({display :''});                
            }
            else{
                $('#woocommerce_Pwacheckout_iopn_dump_url').closest('tr').css({display :'none'});
                //$('#woocommerce_Pwacheckout_iopn_dump_url').val("");
            }
        }

        function showHideMWSOrderDumpFilePath(mws_order){
           if(mws_order ==1){
                $('#woocommerce_Pwacheckout_mws_order_dump_url').closest('tr').css({display :''});                
            }
            else{
                $('#woocommerce_Pwacheckout_mws_order_dump_url').closest('tr').css({display :'none'});
               // $('#woocommerce_Pwacheckout_mws_order_dump_url').val("");
            }
        }

        function showHideMWSReportDumpFilePath(id111,mws_report){
           if(mws_report ==1){
                $('#woocommerce_Pwacheckout_mws_report_dump_url').closest('tr').css({display :''});  
            }
            else{
                $('#woocommerce_Pwacheckout_mws_report_dump_url').closest('tr').css({display :'none'});
                //$('#woocommerce_Pwacheckout_mws_report_dump_url').val("");
            }
        }

        function checkStringEndWithSlash(ids){
              str= $( "#"+ids ).val()
              len= str.length;
              var n = str.lastIndexOf("/");
              if (len-1 != n)
                 str=    str.concat("/");
              $( "#"+ids ).attr('value',str);

        }

function isUrl(s) {
   var re_weburl = /^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i;
		if(re_weburl.test(s)){
		} else {
		  alert("Enter Url is not valid \n "+s);
		  $("#woocommerce_Pwacheckout_mws_endpoint_url").val("");
		}
}


        // check validaion on submit 
$('#mainform').submit(function(event) {

       if(window.location.href.indexOf("pwacheckout") >= 0)   
       {
            if( $.trim( $("#woocommerce_Pwacheckout_access_key").val()) == "" )
            {
                $("#access_key_error").html("Access Key is required ");
    	    $('#woocommerce_Pwacheckout_access_key').css('border-color', 'red');
                event.preventDefault();

            }else{
                $("#access_key_error").html("");
    	    $('#woocommerce_Pwacheckout_access_key').css('border-color', '#DDDDDD');
            }


            if( $.trim( $("#woocommerce_Pwacheckout_secret_key").val()) == "" )
            {
                $("#secret_key_error").html("Secret Key is required ");
     	    $('#woocommerce_Pwacheckout_secret_key').css('border-color', 'red');
                event.preventDefault();

            }else{
                $("#secret_key_error").html("");
    	    $('#woocommerce_Pwacheckout_secret_key').css('border-color', '#DDDDDD');
            }


            if( $.trim( $("#woocommerce_Pwacheckout_merchant_id").val()) == "" )
            {
                $("#merchant_id_error").html("Merchant ID is required ");
    	    $('#woocommerce_Pwacheckout_merchant_id').css('border-color', 'red');
                event.preventDefault();

            }else{
                $("#merchant_id_error").html("");
    	    $('#woocommerce_Pwacheckout_merchant_id').css('border-color', '#DDDDDD');
            }

            if($('#woocommerce_Pwacheckout_pwa_btn_img').length > 0){
                 var file = $('#woocommerce_Pwacheckout_pwa_btn_img').prop('files')[0];         
                 fileName=file.name;
                 fileExtension = fileName.substr((fileName.lastIndexOf('.') + 1));
                 if ( !(fileExtension == 'jpg' || fileExtension == 'jpeg' || fileExtension == 'png' || fileExtension == 'gif' ) ){
                     $("#pwa_btn_img_error").html(" only jpg, jpeg, png, gif extension image allowed!");
                     event.preventDefault();
                 }
                 else if( file.size > 512000 )
                 {
                     $("#pwa_btn_img_error").html("File size larger then 512 KB not allowed");
                     event.preventDefault();

                 }
              }
        }
});


	});

})(jQuery);






