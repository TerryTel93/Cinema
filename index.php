<?php
error_reporting(0);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
$Admin = $_GET['Admin'];
?>

<html>


<head>
        <title>WebSocket TEST</title>
  <link href="css/bootstrap.css" rel="stylesheet">
  <link href="css/bootstrap-responsive.css" rel="stylesheet">
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

   }
   /* custom player skin */
   .flowplayer { width: 80%; background-color: #222; background-size: cover; max-width: 800px; }
   .flowplayer .fp-controls { background-color: rgba(0, 0, 0, 0.4)}
   .flowplayer .fp-timeline { background-color: rgba(0, 0, 0, 0.5)}
   .flowplayer .fp-progress { background-color: rgba(219, 0, 0, 1)}
   .flowplayer .fp-buffer { background-color: rgba(249, 249, 249, 1)}
   .flowplayer { background-image: url('<?php echo $_GET['screen'];?>')}
   </style>
   <!-- flowplayer depends on jQuery 1.7.1+ -->
   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<!-- test from sam -->
   <!-- flowplayer javascript component -->
   <script src="http://releases.flowplayer.org/5.3.2/flowplayer.min.js"></script>

</head>

<script type="text/javascript">

flowplayer(function (api, root) {
 
 api.bind("load", function () {
  }).bind("ready", function () {

  });

 api.bind("pause", function(e, api) {
 log('Pause');socket.send('Pause');
});

 api.bind("resume", function(e, api) {
 log('play');socket.send('play');
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
                    log('Pause');socket.send('Pause');flowplayer().pause();
                  } else {
                     log('play');socket.send('play');flowplayer().play();
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
                        if ( msg.data =='Pause') flowplayer().pause();
                        else if ( msg.data =='play') flowplayer().resume();
                        else 
                            flowplayer().seek(msg.data);
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
        socket.send(time);
        flowplayer().seek(time);
        log (time);
        }     
        </script>

    </head>
    <body onload="init()" color="#000000">

   <div data-engine="html5"
      class="flowplayer" data-fullscreen="false"
      data-ratio="0.5625" data-keyboard="false">
   <video id ='video2' preload="none" style="display:block;width:425px;height:300px;">
      <source type="video/flv" src="<?php echo $_GET['link'];?>"/>
   </video>
</div>

</br> 
</br>
<?php if ($Admin == 1){
    echo '<button class="btn" onclick="test();">Sync</button>';
}
?>

<div id="log"></div>

 

    </body>
</html>
