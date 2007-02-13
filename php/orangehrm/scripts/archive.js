<script>

    // Checks for a valid email address
    //
    // Returns true if a valid email
    // false otherwise.
    function checkEmail(emailStr) {

        // checks if the e-mail address is valid
        //var emailPat = /^(\".*\"|[A-Za-z]\w*|\.)@(\[\d{1,3}(\.\d{1,3}){3}]|[A-Za-z]\w*(\.[A-Za-z]\w*)+)$/;
		var emailPat = /^(([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9])+(\.[a-zA-Z0-9_-]+)+)$/
        /*var matchArray = emailStr.match(emailPat);
        if (matchArray == null) {
            return false;
        }

        // make sure the IP address domain is valid
        var IPArray = matchArray[2].match(/^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/);
        if (IPArray != null) {
            for (var i=1;i<=4;i++) {
                if (IPArray[i]>255) {
                    return false;
                }
            }
        }*/        
        if (emailPat.test(emailStr)) {
        	return true;
        }
	return false;
    }

    //checks that there aren't any numbers

    function alpha(txt)
    {
        var flag=true;
        var i,code;

        if(txt.value=='')
            return false;

        for(i=0;txt.value.length>i;i++)
        {
	       code=txt.value.charCodeAt(i);
    
           if (code>=48 && code<=57) {
	           flag=false;
               break;
           }
	       else
	           flag=true;
	   
	    }
    return flag;
    }

    //check to see whether a valid phone number

    function numeric(txt)
    {
        var flag=true;
    
        for(i=0;txt.value.length>i;i++){
    
            code=txt.value.charCodeAt(i);

            if ( ( (code>=48) && (code<=57) ) || (code == 45) || (code == 43))
                flag=true    
            else
            {
                flag=false;
                break;    
            }
        }    
    return flag;
    }

    function numbers(txt)
	{
    	var flag=true;
    
    	for (i=0;txt.value.length>i;i++) {
    
        	code=txt.value.charCodeAt(i);

        	if ( (code >= 48) && (code <= 57) ) {
        		
            	flag=true;
            } 
        	else
        	{
            	flag=false;
            	break;    
        	}
    	}    
	return flag;
	}
	
	function nonNumbers(txt) {
    
    	var notNum="";
    	var flag=true;
    
    	for (i=0;txt.value.length>i;i++) {
    
        	code=txt.value.charCodeAt(i);

        	if ( (code>=48) && (code<=57) )
            	flag=true   
        	else
        	{
            	flag=false;
            	notNum=notNum+" '"+txt.value.charAt(i)+"'";    
        	}
    	}    
	return notNum;     
	}
	
	function clearAll() {
		//need to work
		document.forms[0].reset('');
	}

</script>
