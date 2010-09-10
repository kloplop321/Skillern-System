<?php
require("include/base.php");
needrights(1);
$title = "Edit profile";
if(isset($_REQUEST['uid'])){
    needrights(7);
    $uid = (real)$b64c->decode($_REQUEST['uid']);
}else{
    $uid = $_SESSION['id'];
}
$sql = "SELECT email, username FROM skllern_users WHERE ID = '" . $uid . "' LIMIT 1";
        $result = sqlite_query($sdb,$sql);
        while ($row = sqlite_fetch_array($result)) {
            $email = $row[0];
            $username = $row[1];
        }
?><div class="bcontent">
<div class="stdwrap">
    <div class="cerror hidden">Error message here</div>
    <div class="csucc hidden">Congratulations, you have updated your info!</div>
    <div class="fpuser">
        <div class="fpusertext flabel">Username</div>
        <div class="fpuserin textf"><input class="fpuserinput" type="text" value="<?php echo $username; ?>" /></div>
        <div class="baduser">The username you sent has been deemed invalid. Therefore it will not be changed...</div>
    </div>
    <div class="fppass">
        <div class="fppasstext1 flabel">Password</div>
        <div class="fppassin textf"><input class="fppassinput" type="password" value="" /></div>
        <div class="noblank">You are NOT allowed to put any blank passwords in. Also, any spacing around the sides will be removed.</div>
    </div>
    <div class="fpveri">
        <div class="fppasstext2 flabel">Verify</div>
        <div class="fppassin textf"><input class="fpveriinput" type="password" value="" /></div>
        <div class="wrongpass">You do not have your password in twice correctly to verify. Therefore it will not be changed...</div>
    </div>
    <div class="fpemail">
        <div class="fpemailtext flabel">Email</div>
        <div class="fpemailin textf"><input class="fpemailinput" type="text" value="<?php echo $email; ?>" /></div>
        <div class="noemail">Your email is not valid. Therefore it won't be changed...</div>
    </div>
    <?php
    if(hasrights(7)){
    ?>
    <div class="fptype">
        <div class="fptypetext flabel">User Type</div>
        <div class="ftypein selectf"><select name="ftypeselect" class="ftypeselect">
                <option value="0">Banned</option>
                <option value="1">Normal student</option>
                <option value="3">Moderator student</option>
                <option value="7">Teacher Assistant</option>
                <option value="8">Teacher</option>
                <option value="10">Web Master</option>
            </select></div>
        <div class="novalue">You somehow did not put in a correct value in the drop down...</div>
    </div>
    <?php
    }
    ?>
</div>
<div class="noleft vspacer8"><!-- --></div>
<div class="fupdate">Update Profile</div>

<div class="goback"><div class="hidden data">apanel.php</div><div class="gbtext">Go Back</div></div>

</div>

<div class="bscript">
<script type="text/javascript">

function onloadedy() {
    $(document).ready(function() {
        $('.mtitle').stop(true, true);
        $('.mtitle').slideUp(300, function(){
                $('.mtitle').html('<?php
                echo $title;
                ?>');
                $('.mtitle').slideDown(300);
        });
        $('.goback').unbind();
        $('.goback').click( function(){
            $('.goback').unbind();
            $.ajax({
                type: "POST",
                url: "apanel.php",
                data: "",
                success: function(data){
                    $('.workingarea').html(data);
                    $('.mcontent').slideUp(400, function(){
                        $('.mcontent').html($('.workingarea').find('.bcontent').html());
                        $('.workingarea').find('.bcontent').html('');
                        $('.mcontent').slideDown(600);
                    });
                }
            });
        });
        $('.fupdate').unbind();
        $('.fupdate').click( function(){
            $('.fupdate').unbind();
            $('.mtitle').slideUp(300, function(){
                $('.mtitle').html('Sending information.');
                $('.mtitle').slideDown(300);
                $.ajax({
                    type: "POST",
                    url: "profile.save.php",
                    data: {u: $('.fpuserinput').val(), p: $('.fppassinput').val(), v:$('.fpveriinput').val(), e: $('.fpemailinput').val()<?php if(hasrights(7)){ ?>
                    ,t:  $('.ftypeselect').val()<?php } ?>},
                    success: function(data){
                        //alert("u=" + noand($('.fpuserinput').val()) + "&p=" + noand($('.fppassinput').val()) + "&v=" + noand($('.fpveriinput').val()) + "&e=" + noand($('.fpemailinput').val()));
                        $('.workingarea').html(data);
                        $('.mtitle').slideUp(300, function(){
                            $('.mtitle').html('<?php
                            echo $title;
                            ?>');
                            $('.mtitle').slideDown(300);
                        });
                    },
                    error: function(){
                        $('.mtitle').slideUp(300, function(){
                            $('.mtitle').html('Error in transfer, try again.');
                            $('.mtitle').slideDown(300);
                        });
                    }
                });
            });
        });
        
    });//end document ready
    
}
function noand(strinput){
    return strinput.replace("&", "_AND_");
}
     function checkloadedy(){
        if($('.workingarea').find('.bcontent').html() == ''){
            onloadedy();
        }else{
            setTimeout('checkloadedy()', 50);
        }
     }
     
     
checkloadedy();
</script></div>