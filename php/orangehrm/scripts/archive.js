<script>

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

</script>
