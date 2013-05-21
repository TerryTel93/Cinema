<?php
error_reporting(0);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
$Admin = $_GET['admin'];
$small = $_GET['small'];
$channel = $_GET['channel'];

$con=mysqli_connect("localhost","root","","cinema_cinema");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

$result = mysqli_query($con,"SELECT * FROM cinema
WHERE channel='$channel'");

while($row = mysqli_fetch_array($result))
  {
$link = $row['link'];
$screen = $row['screen'];
$password = $row['password'];
$title = $row['name'];
  }
?>


<html>


<head>
        <title>WebSocket TEST</title>
  <link href="/projects/SamCinemaPlayer/css/bootstrap.css" rel="stylesheet">
  <link href="/projects/SamCinemaPlayer/css/bootstrap-responsive.css" rel="stylesheet">
   <!-- player skin -->
   <link rel="stylesheet" type="text/css"
      href="http://releases.flowplayer.org/5.3.2/skin/minimalist.css" />
   
   <style>
   /* site specific styling */
   body {
      font: 12px "Myriad Pro", "Lucida Grande", "Helvetica Neue", sans-serif;
      text-align: center;
      padding-top: 1%;
      color: #999;
      background-color: #333333;
    overflow-y: hidden;
   }
   #log {
   text-align: center;
    height:150px;
    overflow-y: scroll;
    overflow-x: hidden;
    <?php if (!$Admin){
       echo "display:none";
    }
    ?>
   }
   h1,h2,h3,h4,h5{
    color: #FFFFFF;
   }
   /* custom player skin */

<?php if($small){
  echo ".flowplayer { width: 80%; background-color: #222; background-size: cover; max-width: 800px; }";
}else
{
  echo ".flowplayer { width: 100%; height: 100% background-color: #222; background-size: cover; }";
}

if ($admin==$password){ 
  echo "   .flowplayer .fp-timeline { display:none;}";
};
?>
   
   .flowplayer .fp-controls { background-color: rgba(0, 0, 0, 0.4)}
   .flowplayer .fp-progress { background-color: rgba(219, 0, 0, 1)}
   .flowplayer .fp-buffer { background-color: rgba(249, 249, 249, 1)}
   .flowplayer { background-image: url('<?php echo $screen;?>')}
   </style>
   <!-- flowplayer depends on jQuery 1.7.1+ -->
   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

   <!-- flowplayer javascript component -->
   <script src="http://releases.flowplayer.org/5.3.2/flowplayer.min.js"></script>

</head>
<script type="text/javascript">

flowplayer(function (api, root) {
 
 api.bind("load", function () {
  }).bind("ready", function () {

  });

 api.bind("pause", function(e, api) {
 log('Channel <?php echo $channel; ?> Pause');socket.send('Channel <?php echo $channel; ?> Pause');
});

 api.bind("resume", function(e, api) {
 log('Channel <?php echo $channel; ?> play');socket.send('Channel <?php echo $channel; ?> play');
 fullscreen()
});

  api.bind("fullscreen", function(e, api) {
});

  api.bind("fullscreen-exit", function(e, api) {
});

});
    var jq=jQuery.noConflict();
    jq(document).ready( function(){

        jq(document).keydown(function(event){
            if (event.which == 32) 
                {
                  if (flowplayer().playing)
                  {
                    log('Pause');socket.send('Channel <?php echo $channel; ?> Pause');flowplayer().pause();
                  } else {
                     log('play');socket.send('Channel <?php echo $channel; ?> play');flowplayer().play();
                  }
                };
        });
    });
</script>
        <script>
            String.prototype.repeat = function(num)
            {
                return new Array(num + 1).join(this);
            }



            var socket;

              function createSocket(host) {

                  if (window.WebSocket)
                      return new WebSocket(host);
                  else if (window.MozWebSocket)
                      return new MozWebSocket(host);

              }

              function init() { 
                var host = "ws://" + window.location.host + ":8080";
                try {
                    socket = createSocket(host);
                    log('WebSocket - status ' + socket.readyState);
                    socket.onopen = function(msg) {
                        log("Welcome - status " + this.readyState);
                    };
                    socket.onmessage = function(msg) { 
                        log("Received (" + msg.data.length + " bytes): " + msg.data);                       
                        if ( msg.data.indexOf("Channel <?php echo $channel; ?> Pause") !=-1) flowplayer().pause();
                        else if ( msg.data.indexOf("Channel <?php echo $channel; ?> play") !=-1) flowplayer().resume();
                        <?php $count = strlen($channel); ?>
                        else if ( msg.data.indexOf("Channel <?php echo $channel; ?> time:") !=-1) flowplayer().seek(msg.data.substr(<?php echo 14+$count; ?>)); log(msg.data);

                         }; 
                    socket.onclose = function(msg) {
                        log("Disconnected - status " + this.readyState);
                    }; function startTime() {
                document.getElementById('time').innerHTML=myVideo.currentTime();};
                }
                catch (ex) {
                    log(ex);
                }
                $("msg").focus();
            }

             function send() {


                var msg = document.getElementById('msg').value;

                try {
                    socket.send(msg);
                    log('Sent (' + msg.length + " bytes): " + msg);
                } catch (ex) {
                    log(ex);
                }
            }

            function quit() {
                log("Goodbye!");
                socket.close();
                socket = null;
            }

            function playPause()
            { 
            if (myVideo.paused) 
              
              socket.send('play'); 
            else 
              myVideo.pause();
              socket.send('pause'); 
            } 

            // Utilities
            function $(id) {
                return document.getElementById(id);
            }
            function log(msg) {
                $("log").innerHTML += "<br>" + msg;
            }
            function onkey(event) {
                if (event.keyCode == 13) {
                    send();
                }
            }

           function test() {
            var api = flowplayer(), currentPos;
            time = api.ready ? api.video.time : 0;
            socket.send("Channel <?php echo $channel; ?> time:"+time);
            flowplayer().seek(time);
            log ("Channel <?php echo $channel; ?> time:"+time);
            }     
        </script>

    </head>
    <body onload="init()" color="#000000">
      <?php if ($link){ ?> 
      <?php if ($Admin == $password){ ?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="span2">
     <button class="btn" onclick="test();">Sync</button>
         <div id="log"></div>
    </div>
    <div class="span10">
         <div data-engine="html5"
      class="flowplayer" data-fullscreen="false"
      data-ratio="0.5625" data-keyboard="false">
   <video id ='video2' preload="none" style="display:block;width:425px;height:300px;">
      <source type="video/flv" src="<?php echo $link;?>"/>
   </video>
            <h1><?php echo $title; ?></h1>
</div>
    </div>
  </div>
</div><?php
}else{
?>

   <div data-engine="html5"
      class="flowplayer" data-fullscreen="false"
      data-ratio="0.5625" data-keyboard="false">
   <video id ='video2' preload="none" style="display:block;width:425px;height:300px;">
      <source type="video/flv" src="<?php echo $link;?>"/>
   </video>
</div>
 <div id="log"></div>
<?php
}
}else{
echo "<h3>Welcome to the cinema. The room '' ".$channel." '' does not exist at this time</h3>";
}
?>
 

    </body>
</html>
