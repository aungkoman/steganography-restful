<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-155533035-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-155533035-1');
</script>


<?php
        $image = isset($_GET['v']) ? $_GET['v'] : null;
        if($image == null ) die("can't find v");
        if (filter_var($image, FILTER_VALIDATE_INT)) {
            //echo("Variable is an integer");
        } else {
            die("only interger is allowed!");
        }
        $image_url = "https://mmsoftware100.com/cipher/images/".$image.".png";
        echo "<img src='$image_url' />";
        //echo "image_url is ".$image_url;
?>

<br>
<form action="../decrypt.php" method="post">



    <input type="password" id="password" name="password" required placeholder="စကားဝှက်">
        <input type="text" name="v" style="display:none"  value="<?php echo $image; ?>">
        
        <input type="submit" value="ပုံဖော်ရန်">
</form>