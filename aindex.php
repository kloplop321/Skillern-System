<?php
require("include/base.php");
if(isset($_SESSION['skillern'])){
    if($_SESSION['user'] == ''){
        unset($_SESSION['skillern']);
    }
}
function login(){
    global $_REQUEST, $_SESSION, $sdb, $cscript, $title;
    $errort = '';
    $username = strtolower(trim($_REQUEST['usern']));
    //test to see if they are trying to get in
    if(isset($_REQUEST['sub'])){
        //check login here
            $password = sha1(trim($_REQUEST['pword']));
        //prepare sql.
        $username = sqlite_escape_string($username);
        $sql = "SELECT ID, password, usertype FROM skllern_users WHERE username = '$username'";
         $result = sqlite_query($sdb, $sql);
         if (sqlite_num_rows($result) > 0) {
            while ($row = sqlite_fetch_array($result)) {
                $id = $row[0];
                $pass = $row[1];
		$rights = $row[2];
            }
        }else{
             $errort = "Incorrect Username or password. Please Try again.";//user not exist
        }
        if ($pass == $password) {
            //correct:
            $_SESSION['user'] = $username;
            $_SESSION['session'] = uniqid();
            $_SESSION['time'] = time();
            $_SESSION['id'] = $id;
            $_SESSION['skillern'] = 1;
            $_SESSION['isin'] = 1;
	    $_SESSION['rights'] = $rights;
            $_SESSION['password'] = sha1($password );
	    
            $sql = "UPDATE skllern_users SET sessionid = '" . $_SESSION['session'] .
                "' WHERE ID = '" . $id . "'  ";
            $result = sqlite_query($sdb,$sql);
            //echo "You is to be logged in? ";
            return null;
        }else{
            //they failed. Time to return the form with an error message.
            $errort = "Incorrect Username or password. Please Try again.";//password not exist.
            //it is better to not identify which does not exist for security reasons.
        }
    }else{
        //they did not send anything. Procede.
    }
    //because we have gotten this far we can infer that they have failed or have not logged in, therefore the from must be presented.
    $html = <<<HT
    <div class="centered">
        <div class="lerror">$errort</div>
	
        <div class="frow"><div class="ftext noselect">Username</div><div class="textf"><input type="text" class="luser" value="$username" name="usern" /></div></div><br />
        <div class="frow"><div class="ftext noselect">Password</div><div class="textf"><input type="password" class="lpass" value="" name="pword" id="lpass" /></div></div><br />
        <br />
        <div class="lbutton noselect" id="lbutton"> Login </div>
    </div>
HT;
if(!isset($_SESSION['skillern'])){
echo $html;
$title = "Please Log in.";
$cscript = <<<JS
        $(".lbutton").livequery('click', function(){
	$(".lbutton").expire();
            $.ajax({
                type: "POST",
                url: "aindex.php",
                data: "sub=1&usern=" + $('.luser').val() + "&pword=" + $('#lpass').val(),
                success: function(data){
                    
                    $('.workingarea').html(data);
                }
            });
        
    });
JS;
}
    return null;
}
?>
<div class="bcontent">
<?php
login();
//session_destroy();
if(!isset($_SESSION['skillern'])){
    //show login prompt
}else{
    //verify that user is for the skillern system
    if(isset($_SESSION['skillern'])){
    
        //hey, welcome to the club, now let me redirect you.
        echo <<<HT
        <div class="centered">
        Hey, welcome to the Skillern system. We&apos;ll bring you to your main panel shortly!
        </div>
HT;
        $title = "Please wait, Preparing your panel...";
        $cscript = <<<JS
        $.ajax({
	    url: 'apanel.php',
	    success: function(data) {
                $('.workingarea').html(data);
                $('.mcontent').slideUp(400, function(){
                    $('.mcontent').html($('.workingarea').find('.bcontent').html());
                    $('.workingarea').find('.bcontent').html('');
                    
                    $('.mcontent').slideDown(600);
                    $('.mcontent').css("background-image", "none");
                });
            }
        });
JS;
    }
}
?>
</div>
<div class="bscript">
<script type="text/javascript">


$(document).ready(function() {
    $('.mtitle').slideUp(400, function(){
        $('.mtitle').html('<?php
        echo $title;
        ?>');
        $('.mtitle').slideDown(600);
        
    });
    $('.mcontent').css("background-image", "url(images/Key-icon.png)");
    $('.mcontent').css("background-repeat", "no-repeat");
    $('.mcontent').css("background-position", "center right");
    $('.mcontent').html($('.workingarea').find('.bcontent').html());
    
    $(".lbutton").expire();
    $('.noselect').livequery( function(){
	$(this).disableTextSelect();
	});
        <?php
        echo $cscript;
        ?>
});
</script>
<div class="time">
    <?php echo time();
    ?>
    </div>
</div>