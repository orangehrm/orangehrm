<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
?>

<?php
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/photoTiling'));
$originalPost = $sf_data->getRaw('originalPost');
$photos = null;
if ($originalPost) {
    $photos = $originalPost->getPhotos();
}
$imgCount = 1;
?>
<?php
if (count($photos) == 1) {
    ?>
    <div class="imageContainer">
        <div class="oneImageOne">
            <img id="<?php echo $imgCount . "_" . $postId; ?>" class="postPhoto" width="100%"
                 src="<?php echo url_for("buzz/viewPostPhoto?id=" . $photos[0]->getId()); ?>"/>
        </div>
    </div>
<?php } else if (count($photos) == 2) {
    ?>
    <div class="imageContainer">
        <div class="twoImageOne">
            <img id="<?php echo "1_" . $postId; ?>"  class="postPhoto" height="120%" 
                 src="<?php echo url_for("buzz/viewPostPhoto?id=" . $photos[0]->getId()); ?>"/>
        </div>
        <div class="twoImageTwo">
            <img id="<?php echo "2_" . $postId; ?>"  class="postPhoto" height="120%" 
                 src="<?php echo url_for("buzz/viewPostPhoto?id=" . $photos[1]->getId()); ?>"/>
        </div>

    </div>

<?php } else if (count($photos) == 3) {
    ?>
    <div class="imageContainer">
        <div class="threeImageOne">
            <img id="<?php echo "1_" . $postId; ?>"  class="postPhoto" height="120%" 
                 src="<?php echo url_for("buzz/viewPostPhoto?id=" . $photos[0]->getId()); ?>"/>
        </div>
        <div class="threeImageTwo">
            <img id="<?php echo "2_" . $postId; ?>"  class="postPhoto" width="120%" 
                 src="<?php echo url_for("buzz/viewPostPhoto?id=" . $photos[1]->getId()); ?>"/>
        </div>
        <div class="threeImageThree">
            <img id="<?php echo "3_" . $postId; ?>"  class="postPhoto" width="120%" 
                 src="<?php echo url_for("buzz/viewPostPhoto?id=" . $photos[2]->getId()); ?>"/>
        </div>
    </div>
<?php } else if (count($photos) == 4) {
    ?>
    <div class="imageContainer">
        <div class="fourImageOne">
            <img id="<?php echo "1_" . $postId; ?>"  class="postPhoto" height ="140%" 
                 src="<?php echo url_for("buzz/viewPostPhoto?id=" . $photos[0]->getId()); ?>"/>
        </div>
        <div class="fourImageTwo">
            <img id="<?php echo "2_" . $postId; ?>"  class="postPhoto" width="120%" 
                 src="<?php echo url_for("buzz/viewPostPhoto?id=" . $photos[1]->getId()); ?>"/>
        </div>
        <div class="fourImageThree">
            <img id="<?php echo "3_" . $postId; ?>"  class="postPhoto" width="120%" 
                 src="<?php echo url_for("buzz/viewPostPhoto?id=" . $photos[2]->getId()); ?>"/>
        </div>
        <div class="fourImageFour">
            <img id="<?php echo "4_" . $postId; ?>"  class="postPhoto" width="140%" 
                 src="<?php echo url_for("buzz/viewPostPhoto?id=" . $photos[3]->getId()); ?>"/>
        </div>
    </div>
<?php } else if (count($photos) == 5) {
    ?>
    <div class="imageContainer">
        <div class="fiveImageOne">
            <img id="<?php echo "1_" . $postId; ?>"  class="postPhoto" height="140%" 
                 src="<?php echo url_for("buzz/viewPostPhoto?id=" . $photos[0]->getId()); ?>"/>
        </div>
        <div class="fiveImageTwo">
            <img id="<?php echo "2_" . $postId; ?>"  class="postPhoto" height="160%" 
                 src="<?php echo url_for("buzz/viewPostPhoto?id=" . $photos[1]->getId()); ?>"/>
        </div>
        <div class="fiveImageThree">
            <img id="<?php echo "3_" . $postId; ?>"  class="postPhoto" width="120%" 
                 src="<?php echo url_for("buzz/viewPostPhoto?id=" . $photos[2]->getId()); ?>"/>
        </div>
        <div class="fiveImageFour">
            <img id="<?php echo "4_" . $postId; ?>"  class="postPhoto" width="140%" 
                 src="<?php echo url_for("buzz/viewPostPhoto?id=" . $photos[3]->getId()); ?>"/>
        </div>
        <div class="fiveImageFive">
            <img id="<?php echo "5_" . $postId; ?>"  class="postPhoto" width="140%" 
                 src="<?php echo url_for("buzz/viewPostPhoto?id=" . $photos[4]->getId()); ?>"/>
        </div>
    </div>
    <?php
}
