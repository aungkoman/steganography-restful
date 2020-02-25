<!DOCTYPE html>
<html lang="en">
        <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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


                <style>
                        .thumb {
                                width: 200px;
                        }
                        .hidden{
                                display:none;
                        }
                </style>
                <title>Image Share</title>
                <link rel="stylesheet" href="css/main.css">
                <link rel="stylesheet" href="css/jquery.loadingModal.min.css">
  </head>

  <body>
        <form method="post" id="send_form">
                <input type="file" id="original_photo" accept="image/x-png,image/gif,image/jpeg" onchange="readURL(this);"  name="original_photo"  required />
                <br>
                <!--span id="cover_photo_span"></span-->
                <img id="original_photo_img"  class="thumb" alt="ပေးပို့လိုသော ဓာတ်ပုံရွေးချယ်ပေးပါ">
                <br>
                <input type="text" class="form-control" placeholder="စကားဝှက် " name="password" required>
                <br>
                <br>
                <button type="submit" class="btn btn-default" id="send_button">ပေးပို့မည်</button>
                <br>
        </form>

        <div id="decrypt_div">
                <span id="upload_percent_span"></span>
                <br>
                <progress value="0.4" id="progress_bar_id"></progress>
        </div>
    </div> <!-- /container -->

<div id="sharable_div">
        <input style="width:100%" type='text' id='sharable_link' />
        <br><br>
        <button class="btn btn-primary"   onclick="copySharableLink()">Copy Link</button>
</div>


    <script>
function copySharableLink() {
  var copyText = document.getElementById("sharable_link");
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");
  console.log("Copied the text: " + copyText.value);
}
</script>



    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.min.js"></script>

<script>
  if (window.File && window.FileReader && window.FileList && window.Blob) {
  // Great success! All the File APIs are supported.
  console.log("The File APIs are fully supported by your browser");
} else {
  alert('The File APIs are not fully supported in this browser.');
}

// original image preview
function readURL(input) {//================Photo ???????????????????????????????????====================
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#original_photo_img').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
    }
  
    
// cover image preview
function readURLcover(input) {//================Photo ???????????????????????????????????====================
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cover_photo_img').attr('src', e.target.result);
                var cover_photo_data = document.getElementById('cover_photo_data');
                cover_photo_data.value = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
    }
</script>


    <script src="js/jquery.js"></script>
  <script src="js/jquery.loadingModal.min.js"></script>
    <script>
//       $("#send_button").on("click",function(){
//         //show_loading_modal("ဓာတ်ပုံများ ဆာဗာသို့ ပေးပို့နေပါပြီ  ... ");
//       })

      $(document).ready(function(){
              console.log("document is ready");
                $("#decrypt_div").hide();
                $("#sharable_div").hide();
              $("#send_form").on('submit',function(e){
                        e.preventDefault();
                        //var fd = new FormData(this);
                        var formdata = new FormData(this);
                        console.log("send_form is submited");
                        $(this).hide();
                        $("#decrypt_div").show();
                        $.ajax({
                                xhr: function() {
                                        var xhr = new window.XMLHttpRequest();
                                        xhr.upload.addEventListener("progress", function(evt) {
                                                console.log("progress bar is updated ");
                                                if (evt.lengthComputable) {

                                                        var percentComplete = evt.loaded / evt.total;
                                                        percentComplete = parseInt(percentComplete * 100);
                                                        $("#upload_percent_span").text("Uploading... .."+percentComplete);
                                                        console.log(percentComplete);
                                                        if(percentComplete == 100){
                                                                $("#upload_percent_span").text("Decrypting...");
                                                        }
                                                }
                                        }, false);
                                        return xhr;
                                },
                                url: "send_form2.php",
                                type: "POST",
                                data: formdata,
                                // Tell jQuery not to process data or worry about content-type
                                // You *must* include these options!
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function(result) {
                                        console.log(result);
                                        if(result.status){
                                                $("#upload_percent_span").text("Complete");
                                                //$("#upload_percent_span").text(result.data);
                                                $("#sharable_div").show();
                                                $("#progress_bar_id").hide();
                                                $("#sharable_link").val(result.data);
                                        }else{
                                                $("#upload_percent_span").text(result.msg);
                                        }
                                },
                                error: function(err){
                                        console.log("error is "+err);
                                }
                        });
                        
                        console.log("ajax request is sent");
              })
      });
    </script>

  </body>
</html>
