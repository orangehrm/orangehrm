    // Checks for a valid email address
    //
    // Returns true if a valid email
    // false otherwise.
    function checkEmail(emailStr) {

        // checks if the e-mail address is valid
		var emailPat = /^(([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*(\.[a-zA-Z]+))$/;

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
    // This function and calls to it should be removed and replaced with the checkPhone() function below.
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


    //check to see whether a valid phone number
    function checkPhone(txt)
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

	/**
	 * Trims any leading zeros from a number
	 */
	function trimLeadingZeros(num) {
		while (num.substr(0,1) == '0' && num.length>1) {
			num = num.substr(1,9999);
		}
		return num;
	}

	/**
	 * Prototype framework like function to access elements by Id
	 */
	function $(id) {
		return document.getElementById(id);
	}

	/**
	 * Trim whitespace from a string.
	 */
	function trim(s) {
		return s.replace(/^\s+|\s+$/g,"");
	}
