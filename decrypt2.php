<?php
	$password = isset($_POST['password']) ? $_POST['password'] : null;
    $password = htmlspecialchars($password);
	$image_code = isset($_POST['v']) ? $_POST['v'] : null;
	if($password == null || $image_code == null ) die("password and image code  is needed ");

?>
<!DOCTYPE html>
<html lang="en">
  <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-155533035-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-155533035-1');
</script>
  <script src="js/jquery.min.js"></script>


  <style>
                        .thumb {
                                width: 200px;
                        }
                        .hidden{
                                display:none;
                        }
                </style>

  </head>

  <body>

    <div class="container">


<?php
// set miximum executin limit in second
// in this case 5 minutes
set_time_limit(300);
ini_set('memory_limit','-1');

###############################################################


# photo resources
$gray_photo = false; // image resource
$stegano_photo = false; // image resource

$image_filename = $image_code;
$gray_photo_url = "images/gray_".$image_filename;
$stegano_photo_url = "images/".$image_filename;

list($width, $height, $type, $attr) = getimagesize($stegano_photo_url);
$default_width = $width;
$default_height = $height;

function make_eight_bit($binary_string){
	//echo "binary string is ".$binary_string . "and it's count is ".strlen($binary_string); 
	$add_word_count = 8 - strlen($binary_string);
	//echo "<br> add word count is ".$add_word_count ;
	$add_word = "";
	for($i = 0 ; $i <$add_word_count ; $i++){
		$add_word .= "0";
	}
	$binary_string = $add_word.$binary_string;	
	return $binary_string;
}


# read stegano photo as resource
$stegano_photo = imagecreatefrompng($stegano_photo_url) ;
$gray_photo = $stegano_photo;
# initialize otsu threshold by giving image url
require('pwa_otsu.php');
$pwa_otsu = new Pwa_otsu($stegano_photo_url);
# initialize columanr transposition 
require('pwa_columnar.php');
$pwa_columnar = new Pwa_columnar();

$server_key = "whatisthislifeiffullofcare0wehavenotime0tostandandstarewhatisthislifeiffullofcare0wehavenotime0tostandandstarewhatisthislifeiffullofcare0wehavenotime0tostandandstarewhatisthislifeiffullofcare0wehavenotime0tostandandstarewhatisthislifeiffullofcare0wehavenotime0tostandandstarewhatisthislifeiffullofcare0wehavenotime0tostandandstarewhatisthislifeiffullofcare0wehavenotime0tostandandstarewhatisthis34256";
$user_key = $password.$server_key;

###########################################################################################


# getting one dimenstional array 
$transposited_gray_array = array();

$c = 0 ;
for($x = 0 ; $x<$default_width; $x++){
	$total = 800;
	$now = $x+1 ;
	//$percent = (100 * $now ) / $default_width ;
	//$percent = ceil($percent); // Round numbers up to the nearest integer:
	//$pwa_otsu->send_to_browser("( ၁ / ၃ ) စာကို စီစစ်ခြင်း  ".$percent.  " %  ပြီးစီးနေပါပြီ");
	//$pwa_otsu->update_progressbar($percent);
	
	for($y =0; $y<$default_height; $y++){
		# read stegano image pixel value and extract r,g,b decimal
		$rgb = imagecolorat($stegano_photo, $x, $y);
		$r = ($rgb >> 16) & 0xFF; // Get R value 0-255 , last 3 digit
		$g = ($rgb >> 8) & 0xFF; // Get G value 0-255, last 3 digit
		$b = $rgb & 0xFF; // Get B value 0-255, last 2 digit

		# substract binary code from RGB 
		$r_binary = decbin($r);
		$r_binary = make_eight_bit($r_binary);
		$r_replace_value = substr($r_binary,5,3) ;

		$g_binary = decbin($g);
		$g_binary = make_eight_bit($g_binary);
		$g_replace_value = substr($g_binary,5,3) ;

		$b_binary = decbin($b);
		$b_binary = make_eight_bit($b_binary);
		$b_replace_value = substr($b_binary,6,2) ;

		$original_eight = $r_replace_value.$g_replace_value.$b_replace_value;
		$original_decimal = bindec($original_eight);


		// we have to make 8 digit by combining 3 phase
		// and change to decimal


		// making one dimensional binary array ( may be sorted 2 d table )
		$transposited_gray_array[$c] = $original_decimal ;
		$c++;
	}
}



################################################################################################

//$pwa_otsu->send_to_browser("( ၂  /  ၃  ) စကားဝှက် အသုံးပြုရန် ပြင်ဆင်နေပါပြီ... ");
//$pwa_otsu->update_progressbar(80);

$re_transposited_gray_array = $pwa_columnar->re_transposition($transposited_gray_array,$user_key);

//echo "<h3>re_transposited_binary_array count =>  ".count($re_transposited_binary_array) ."</h3>";

$c = 0 ;
for($x = 0 ; $x<$default_width; $x++){
	$now = $x+1 ;
	//$percent = (100 * $now ) / $default_width ;
	//$percent = ceil($percent); // Round numbers up to the nearest integer:
	//$pwa_otsu->send_to_browser(" ( ၃ /  ၃ ) စကားဝှက် အသုံးပြုပြီး ပုံဖော်ခြင်း လုပ်ငန်း   ".$percent.  " %  ပြီးစီးနေပါပြီ");
	//$pwa_otsu->update_progressbar($percent);
	
	for($y =0; $y<$default_height; $y++){
		$current_index = ($x*$default_width) + ($y);
		# get binary value
		$gary_value = $re_transposited_gray_array[$c];
		$c++;


		$newColor = $gary_value  << 16 | $gary_value << 8 | $gary_value ;

		// set color at x , y on binary image
		imagesetpixel($gray_photo, $x, $y, $newColor);
	}
}



//echo "debug array => ".json_encode($debug_array);
# write blank photo
# this photo will be retrieved binary (balck and white ) photo 
# write cover resource (may be it 'is stegano image ')
imagepng($gray_photo,$gray_photo_url);
// read 
# https://stackoverflow.com/questions/30354827/php-display-image-resource
$type = pathinfo($gray_photo_url, PATHINFO_EXTENSION);
$data = file_get_contents($gray_photo_url);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

echo "<br><h2> စကားဝှက် အသုံးပြုပြီး ပြန်လည်ထုတ်ယူရရှိလာသော  အဖြူအမည်း ဓာတ်ပုံ</h2>";
echo "<br>";
echo "<img src='".$base64."' class='thumb'><br>";


//$pwa_otsu->send_to_browser("ပုံဖော်ခြင်း လုပ်ငန်း ပြီးဆုံးပါပြီ ");

//echo "<h3><a href='index.php'> မူလ စာမျက်နှာသို့ ပြန်သွားရန်</a></h3>";

// # Defining Photo URL
// $file_to_delete = array($gray_photo_url);
// //$file_to_delete=array();
// for($i = 0 ; $i<count($file_to_delete); $i++){
// 	if (!unlink($file_to_delete[$i])) {  
// 		//echo ("$file_to_delete[$i] cannot be deleted due to an error");  
// 	 }else {  
// 		//echo ("$file_to_delete[$i] has been deleted");  
// 		//echo "<br>";
// 	} 
// }

?>

          </div>

    </div> <!-- /container -->

    
  </body>
</html>
