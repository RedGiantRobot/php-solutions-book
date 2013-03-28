<?php
$max = 51200;
if (isset($_POST['upload'])) {
    //define the path to the upload folder
    $destination = '/Users/Design1/CATMEDIA/testserver/upload_test/';
    
    
    require_once('../classes/Ps2/Upload.php');
    try{
        // instantiate the upload class
        $upload = new Ps2\Upload($destination);
        $upload->setMaxSize($max);
        //$upload->setPermittedTypes(array('text/plain'));
        $upload->move();
        
        
        // if any messages are returned, store them in $result
        $result = $upload->getMessages();        
    } catch (Exception $e) {
        echo $e->getMessages();
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset=utf-8">
<title>Upload File</title>
</head>

<body>
<?php
if (isset($result)) {
    echo '<ul>';
    foreach ($result as $message) {
        echo "<li>$message</li>";
    }
echo '</ul>';
}
?>
<form action="" method="post" enctype="multipart/form-data" id="uploadImage">
  <p>
    <label for="image">Upload image:</label>
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max; ?>"></input>
    <input type="file" name="image[]" id="image" multiple>
  </p>
  <p>
    <input type="submit" name="upload" id="upload" value="Upload">
  </p>
</form>
    
</body>
</html>