<?php
        // set miximum executin limit in second
        // in this case 5 minutes
        set_time_limit(300);
        ini_set('memory_limit','-1');
        header('Content-Type: application/json');

        //print_r($_POST);
        $password = isset($_POST['password']) ? $_POST['password'] : null;
        $password = htmlspecialchars($password);
        if( $password == null){
                return_fail("password have to be provided");
        }
        //print_r($_FILES);
        if(!isset($_FILES['original_photo']['name'])) return_fail('original_photo has to include');
        $milliseconds = round(microtime(true) * 1000);
        $random_no = rand(0,100000);
        $randrom_prefix = $random_no.$milliseconds; # ender with random number 
        $image_filename = $randrom_prefix;//.basename( $_FILES['original_photo']['name']) ;
        
        //echo "file name is ".$image_filename;
        # get file name and server cover name url
        $original_photo_url = "images/original_".$image_filename; // real image
        $cover_photo_url = "images/cover_image.jpg"; // real cover image images/cover_image.jpg
        $gray_photo_url = "images/gray_".$image_filename;
        $stegano_photo_url = "images/".$image_filename;

        
        // # photo resources
        $original_photo = false; // image resource
        $cover_photo = false; // image resource
        $gray_photo = false; // image resource
        $stegano_photo = false; // image resource

        // # width and height
        $default_width = null;
        $default_height = null;

        $server_key = "whatisthislifeiffullofcare0wehavenotime0tostandandstarewhatisthislifeiffullofcare0wehavenotime0tostandandstarewhatisthislifeiffullofcare0wehavenotime0tostandandstarewhatisthislifeiffullofcare0wehavenotime0tostandandstarewhatisthislifeiffullofcare0wehavenotime0tostandandstarewhatisthislifeiffullofcare0wehavenotime0tostandandstarewhatisthislifeiffullofcare0wehavenotime0tostandandstarewhatisthis34256";
        $user_key = $password.$server_key;

        $gray_one_d_array = array();

        /*
                1. write original photo from temp to real  folder

        */
        $file_type=$_FILES['original_photo']['type'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        @$mimeChk2 = finfo_file($finfo, $_FILES["original_photo"]["tmp_name"]);
        //echo "<br> file type : ".$mimeChk2;
        if($mimeChk2 == "image/jpeg" || $mimeChk2 == "image/png"){
                if(move_uploaded_file($_FILES['original_photo']['tmp_name'], $original_photo_url)){
                        // upload success    
                }else {
                        return_fail('upload fail');
                }
        }else { 
                return_fail('only jpeg / png  can be accepted.');
        }

        # assign to image resource
        if($mimeChk2 == 'image/png') $original_photo = imagecreatefrompng($original_photo_url) ; 
        if($mimeChk2 == 'image/jpeg') $original_photo = imagecreatefromjpeg($original_photo_url) ; 
        list($width, $height, $type, $attr) = getimagesize($original_photo_url);
        $default_width = $width;
        $default_height = $height;
        # apply original photo GRAY filter
        imagefilter($original_photo, IMG_FILTER_GRAYSCALE);
        # write gray photo to specific folder
        imagepng($original_photo, $gray_photo_url);


        $cover_photo = imagecreatefromjpeg($cover_photo_url) ; 
        $cover_photo = imagescale($cover_photo,$default_width,$default_height,IMG_NEAREST_NEIGHBOUR);
        # write cover_photo to specific folder 

        
        # major two resources gray and cover 
        # read gray photo resource
        $gray_photo = imagecreatefrompng($gray_photo_url);
        # copy resized cover photo to stegano hoto
        $stegano_photo = $cover_photo; # copy image resource




# make 8 digit binary staring
function make_eight_bit($binary_string){
	$add_word_count = 8 - strlen($binary_string);
	$add_word = "";
	for($i = 0 ; $i <$add_word_count ; $i++){
		$add_word .= "0";
	}
	$binary_string = $add_word.$binary_string;	
	return $binary_string;
}



require('pwa_otsu.php');
$pwa_otsu = new Pwa_otsu($gray_photo_url);



require('pwa_columnar.php');
$pwa_columnar = new Pwa_columnar();


// getting one dimensional  gary value array 
for($x = 0 ; $x<$default_width; $x++){
	$now = $x+1 ;
	//$pwa_otsu->send_to_browser(" ပုံကို စီစစ်ခြင်းလုပ်ငန်း  ".$percent.  " % ပြီးစီးနေပါပြီ။");
	//$pwa_otsu->update_progressbar($percent);
	for($y =0; $y<$default_height; $y++){
		$gray_value = imagecolorat($gray_photo, $x, $y); // Getting RGB pair
		$gray_decimal = ($gray_value >> 16) & 0xFF; // getting R value ; Shifting
		$gray_one_d_array[] = $gray_decimal ;
	}
}


// //$pwa_otsu->send_to_browser("Binary Transposition လုပ်ငန်း စတင်နေပါပြီ");
// $pwa_otsu->update_progressbar(10);
$gray_transposited_one_d_array = $pwa_columnar->transposition($gray_one_d_array,$user_key);


# add to cover photo lsb
$c = 0 ;
for($x = 0 ; $x<$default_width; $x++){
	$now = $x+1 ;
	for($y =0; $y<$default_height; $y++){
		# getting gray decimal
		$gray_decimal = $gray_transposited_one_d_array[$c];
		$c++; // make one day LOSE :(
		# change decimal to binary
		$gray_binary = decbin($gray_decimal);
		# make 8 digit binary staring
		$gray_binary = make_eight_bit($gray_binary);
		# sub three phase
		$red_three = substr($gray_binary,0,3);
		$green_three = substr($gray_binary,3,3);
		$blue_two = substr($gray_binary,6,2);

		$rgb = imagecolorat($cover_photo, $x, $y);
		$r = ($rgb >> 16) & 0xFF; // Get R value 0-255
		$g = ($rgb >> 8) & 0xFF; // Get G value 0-255
		$b = $rgb & 0xFF; // Get B value 0-255

		$r_binary = decbin($r); 
		$r_binary = make_eight_bit($r_binary); 
		$r_msb_5 = substr($r_binary,0,5) ;
		$r_8_bit = $r_msb_5.$red_three; 
		$r = bindec($r_8_bit); 

		$g_binary = decbin($g); 
		$g_binary = make_eight_bit($g_binary); 
		$g_msb_5 = substr($g_binary,0,5) ;
		$g_8_bit = $g_msb_5.$green_three; 
		$g = bindec($g_8_bit); 

		$b_binary = decbin($b); 
		$b_binary = make_eight_bit($b_binary); 
		$b_msb_6 = substr($b_binary,0,6) ;
		$b_8_bit = $b_msb_6.$blue_two; 
		$b = bindec($b_8_bit); 

		$a = $rgb & 0xFF000000;

		$newColor = $a | $r  << 16 | $g << 8 | $b ;
		#$newTestColor = $a | $gray_decimal  << 16 | $gray_decimal << 8 | $gray_decimal ;
		// set color at x , y on image
		imagesetpixel($stegano_photo, $x, $y, $newColor);
		#imagesetpixel($stegano_test_photo, $x, $y, $newTestColor);
	}
}

imagepng($stegano_photo,$stegano_photo_url); # write image resource to file path
// #imagepng($stegano_test_photo,$stegano_test2_photo_url); # write image resource to file path


// //echo "<br><h2>Steganograpy Image </h2>";
// //echo "<img src='".$stegano_photo_url."'><br>";
// #echo "<br><h2>Steganograpy Test Image </h2>";
// #echo "<img src='".$stegano_test_photo_url."'><br>";



// //$pwa_otsu->send_to_browser(" ပုံဖျတ်ခြင်း နှင့် ပုံဝှက်ခြင်း လုပ်ငန်း ပြီးပါပြီ ");


// # Defining Photo URL
// //,$original_photo_temp_url,$cover_photo_temp_url
// $file_to_delete = array($original_photo_temp_url,$cover_photo_temp_url,$original_photo_url,$cover_photo_url,$gray_photo_url);
// //$file_to_delete=array();
// for($i = 0 ; $i<count($file_to_delete); $i++){
// 	if (!unlink($file_to_delete[$i])) {  
// 		//echo ("$file_to_delete[$i] cannot be deleted due to an error");  
// 	 }else {  
// 		//echo ("$file_to_delete[$i] has been deleted");  
// 		//echo "<br>";
// 	} 
// }
// //$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
// $shareable_link = "https://mmsoftware100.com/cipher/image/".$image_filename;
$shareable_link = "http://localhost/cipher/image/".$image_filename;
//echo "shareable link is ".$shareable_link;
return_success("encrypted",$shareable_link);


// echo "<h3>Share your image </h3>";

function return_success($msg,$data = array()){
        $return_obj['status'] = true;
        $return_obj['msg'] = $msg;
        $return_obj['data'] = $data;
        //return $return_obj;
        echo json_encode($return_obj);
        exit;
    }
    function return_fail($msg,$data=array()){
        $return_obj['status'] = false;
        $return_obj['msg'] = $msg;
        $return_obj['data'] = $data;
        //return $return_obj;
        echo json_encode($return_obj);
        exit;
    }
				
?>


