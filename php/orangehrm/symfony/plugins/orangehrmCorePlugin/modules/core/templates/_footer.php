<?php
$styleSheet = 'orange';
$imagePath = public_path("../../themes/{$styleSheet}/images/login");
$version = '2.7.1';
$copyrightYear = '2012';
?>

<style type="text/css">
    #divFooter {
        text-align: center;
    }
    
    #spanCopyright, #spanSocialMedia {
        padding: 20px 10px 10px 10px;
    }
    
    #spanSocialMedia a img {
        margin-bottom: -8px;
		border: none;
    }

</style>
<div id="divFooter" >
    <span id="spanCopyright">
        <a href="http://www.orangehrm.com" target="_blank">OrangeHRM</a> 
        ver <?php echo $version; ?> &copy; OrangeHRM Inc. 2005 - <?php echo $copyrightYear; ?> All rights reserved.
    </span>
    <span id="spanSocialMedia">
        <a href="http://www.linkedin.com/groups?home=&gid=891077" target="_blank">
            <img src="<?php echo "{$imagePath}/linkedin.png"; ?>" /></a>&nbsp;
        <a href="http://www.facebook.com/OrangeHRM" target="_blank">
            <img src="<?php echo "{$imagePath}/facebook.png"; ?>" /></a>&nbsp;
        <a href="http://twitter.com/orangehrm" target="_blank">
            <img src="<?php echo "{$imagePath}/twiter.png"; ?>" /></a>&nbsp;
        <a href="http://www.youtube.com/results?search_query=orangehrm&search_type=" target="_blank">
            <img src="<?php echo "{$imagePath}/youtube.png"; ?>" /></a>&nbsp;
    </span>
    <br class="clear" />
</div>