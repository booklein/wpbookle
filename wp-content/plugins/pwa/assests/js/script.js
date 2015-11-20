function showHidePlaceOrder_PWAButton(btnvalue){
	if(window.location.href.indexOf("cart") >= 0)
	{
		CBA.jQuery('#cbaButton').css({display : 'block'});
	}
	else
	{
		if(btnvalue == 'Pwacheckout')
		{
			CBA.jQuery('#place_order').css({display : 'none'});
			CBA.jQuery('#cbaButton').css({display : 'block', float :'right'});
		}
		else
		{
			CBA.jQuery('#place_order').css({display : ''});
			CBA.jQuery('#cbaButton').css({display : 'none'});
		}
	}
}


	
