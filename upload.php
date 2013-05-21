<?php

$vid_link = $_GET["link"];
$vid_img = $_GET["screen"];
$vid_title = $_GET["title"];

if ($vid_link) {
	$add1 = 'disabled="disabled"';	
}
if ($vid_img){
	$add2 = 'disabled="disabled"';	
}

$con=mysqli_connect("localhost","root","","cinema_cinema");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

$result = mysqli_query($con,"SELECT * FROM cinema");


$name = $_POST['name'];
$image = $_POST['image'];
$link = $_POST['link'];
$mypassword = $_POST['mypassword'];
$channel = $_POST['channel'];

$result1 = mysqli_query($con,"SELECT * FROM cinema WHERE channel='".$channel."'" );
if (!$channel OR !$name OR !$image OR !$link)
{
	$failed = "Unable to upload";
}
else
{
	if(mysqli_num_rows($result1) <1)
	{ 
		mysqli_query($con,"INSERT INTO cinema (name, link, screen, channel, password)
		VALUES ('$name', '$link', '$image', '$channel', '$mypassword')");
		$sucess =  "Uploaded";
	}
	else
	{ 

		while($row = mysqli_fetch_array($result1))
	  	{	
			if ($row['password'] == $mypassword)
			{
				mysqli_query($con,"UPDATE cinema SET name=$name, link=$link,screen=$image
				WHERE channel=$channel");
				$sucess1 = "Updated";
			}
			else
			{
				$failed1 = "Failed to upload";
			}
		}
	} 
}


?>
<html>
<head>

  <link href="http://carr-home.dyndns.org/bootstrap/css/bootstrap.css" rel="stylesheet">
  <link href="http://carr-home.dyndns.org/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
</head>
<body>
 <div class="container">
 	<br>

<?php if ($sucess){ ?>
	<div class="alert alert-success">
	  <h4>Channel <?php echo $channel; ?> Has Been Created.
	    <a href="/video.php?admin=<?php echo $mypassword; ?>&channel=<?php echo $channel; ?>" class="btn btn-large pull-right">Continue</a>
	  </h4>
	  <p>Click on the <b>BIG</b> Continue button to go to the channel.</p>
	</div>
<?php }
elseif ($sucess1){
	?>
	<div class="alert alert-success">
  <h4>Channel <?php echo $channel; ?> Has Been Updated.
    <a href="/video.php?admin=<?php echo $mypassword; ?>&channel=<?php echo $channel; ?>" class="btn btn-large pull-right">Continue</a>
  </h4>
  <p>Click on the <b>BIG</b> Continue button to go to your new channel.</p>
</div>
	<?php
}
elseif ($failed1){
		?>
	<div class="alert alert-error">
  <h4>$failed
  </h4>
  <p>Please try again</p>
</div>
	<?php
}
elseif ($failed){
		?>
	<div class="alert alert-error">
  <h4><?php echo $failed; ?>
  </h4>
  <p>Fuck knows why?  Just ignore it, I do </p>
</div>
	<?php
}
?>

<form class="form-horizontal" action="" method="post">
	<fieldset>
		<div id="legend" class="">
			<legend class="">Creating A Channel</legend>
		</div>
		<div class="control-group">
			<!-- Text input-->
			<label class="control-label" for="input01">Channel Name</label>
			<div class="controls">
			<!-- Select Basic -->	
			<input  type="text" list="channel" placeholder="Channel Name" name="channel" class="input-block-level">
			<datalist id="channel">
<?php
while($row = mysqli_fetch_array($result))
  {
  echo '<option value="'.$row['channel'].'">'. "\n";
  }
?>
			
			</datalist>

				<br><br>
			</div>
		</div>
		<div class="control-group">

			<!-- Text input-->
			<label class="control-label" for="input01">Video Name</label>
			<div class="controls">
				<input  type="text"  placeholder="Video Name" value="<?php echo $vid_title; ?>" name="name" class="input-block-level">
				<br><br>
			</div>
		</div>
		<div class="control-group">

			<!-- Text input-->
			<label class="control-label" for="input01">Video Link</label>
			<div class="controls">
				<input  type="text" value="<?php echo $vid_link; ?>" name="link" placeholder="Video Link" class="input-block-level">
				<br><br>
			</div>
		</div>
		<div class="control-group">

			<!-- Text input-->
			<label class="control-label" for="input01">Image Overlay</label>
			<div class="controls">
				<input type="text"  value="<?php echo $vid_img; ?>" name="image" placeholder="Image Overlay" class="input-block-level">
				<br><br>
			</div>
		</div>
		<div class="control-group">

			<!-- Text input-->
			<label class="control-label" for="input01">Admin Password</label>
			<div class="controls">
				<input type="password" placeholder="Password" name="mypassword" class="input-block-level">
				<br><br>
			</div>
		</div>

		<!-- Button (Double) -->
<div class="control-group">
  <label class="control-label"></label>
  <div class="controls">
    <button id="button1id" name="button1id" class="btn btn-success">Create Channel</button>&nbsp;&nbsp;
    <button id="button2id" name="button2id" class="btn btn-danger">Cancel</button>
  </div>
</div>
</div>
    </fieldset>
  </form>
</body>
<script src="http://carr-home.dyndns.org/bootstrap/js/bootstrap.js"></script>
<script src="http://carr-home.dyndns.org/bootstrap/js/jquery.js"></script>
	</html>