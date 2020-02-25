<style>
                        .thumb {
                                width: 200px;
                        }
                        .hidden{
                                display:none;
                        }
                </style>
<?php
        $image = isset($_GET['v']) ? $_GET['v'] : null;
        //echo "image v is ".$image;
        

        if($image == null ) die("can't find v");
        if (filter_var($image, FILTER_VALIDATE_INT)) {
            //echo("Variable is an integer");
        } else {
            die("only interger is allowed!");
        }

        
        $request_image = "../images/".$image;
        // if (file_exists($request_image)) {
        //         echo "The file $request_image exists";
        //     }else {
        //         echo "The file $request_image does 
        //                                not exists";
        //     }
        if (@getimagesize("images/".$image)) {

        } else{
                echo "<h3>file does not found</h3>";
                die;
        }


                //echo "<h3>request</h3>";
                //echo "<img src='$request_image'  class='thumb'/>";
        echo "<div id='image_div'>";
        echo "<h3>Enter password to view actual image </h3>";
        //$image_url = "https://mmsoftware100.com/cipher/images/".$image.".png";
        $image_url = "../images/cover_image.jpg";
        echo "<img src='$image_url'  class='thumb' id='cover_image'/>";
        echo "<br>";
        echo "</div>";
        //echo "image_url is ".$image_url;
?>

<br>
<form action="../decrypt2.php" method="post" id="decrypt_form">
        <br>
        <input type="password" id="password" name="password" required placeholder="စကားဝှက်">

        <input type="text" name="v" style="display:none"  value="<?php echo $image; ?>">
        <br><br>
        <input type="submit" value="ပုံဖော်ရန်">
</form>
<div id="decrypt_div">
        <span>Decrypting..</span><br>
        <progress value="0.4"></progress>
</div>

<script>
function logSubmit(event) {
  var txt = `Form Submitted! Time stamp: ${event.timeStamp}`;
  console.log("txt is "+txt);
  form.style.display = "none";
  image_div.style.display = "none";
  decrypt_div.style.display = "block";
  //event.preventDefault();
}

const form = document.getElementById('decrypt_form');
const decrypt_div = document.getElementById('decrypt_div');
const image_div = document.getElementById('image_div');
decrypt_div.style.display = "none";
form.addEventListener('submit', logSubmit);

</script>