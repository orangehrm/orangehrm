/**
 * Password Strength - jQuery plugin to check password strength
 * http://labs.rnzmedia.co.za/
 * @requires jQuery Library: http://jquery.com/
 * 
 * Copyright (c) 2011 Riaz Sabjee
 * 
 * Dual licensed under the MIT and GPL licenses:
 * http://www.gnu.org/licenses/gpl.html
 * 
 */

(function(b){b.fn.extend({password:function(e){e=b.extend({score:""},e);return this.each(function(){var g=e;b(this).keyup(function(){var c=b(this).val(),f=g.score,e=password_user.split(","),a=0;c.length>6&&a++;c.match(/[a-z]/)&&c.match(/[A-Z]/)&&a++;c.match(/\d+/)&&a++;c.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/)&&a++;c.length>12&&a++;if(a==0)var d="#fcff00";a==1&&(d="#ffb400");a==2&&(d="#ff8400");a==3&&(d="#ff4e00");a==4&&(d="#93b40f");a==5&&(d="#bde813");c.length> 0?(b(f).css("background-color",d),b(f).html(e[a])):(b(f).css("background-color","transparent"),b(f).html(""))})})}})})(jQuery);
