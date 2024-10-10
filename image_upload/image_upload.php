<?php
$host = 'localhost';
$dbname = 'image_upload';
$username = 'root'; // Your DB username
$password = ''; // Your DB password

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle image upload
if (isset($_POST['upload'])) {
    $imageName = $_FILES['image']['name'];
    $imageData = file_get_contents($_FILES['image']['tmp_name']);
    $imageType = $_FILES['image']['type'];

    if (substr($imageType, 0, 5) == "image") {
        // Prepare SQL query
        $stmt = $conn->prepare("INSERT INTO images (name, image) VALUES (?, ?)");
        $stmt->bind_param("sb", $imageName, $imageData);
        $stmt->send_long_data(1, $imageData);

        if ($stmt->execute()) {
            echo "Image uploaded successfully.";
        } else {
            echo "Failed to upload image.";
        }

        $stmt->close();
    } else {
        echo "Please upload a valid image file.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Image Upload</title>
</head>
<body>
    <h1>Upload Image</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <label>Select Image:</label>
        <input type="file" name="image" required>
        <input type="submit" name="upload" value="Upload">
    </form>

    <h2>Uploaded Images</h2>
    <?php
    // Retrieve and display images from the database
    $sql = "SELECT * FROM images";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div>';
            echo '<h3>' . htmlspecialchars($row['name']) . '</h3>'; // Display image name
            echo '<img src="data:image/jpeg;base64,'.base64_encode($row['image']).'" style="max-width: 200px; margin: 10px;"/>';
            echo '</div>';
        }
    } else {
        echo "No images found.";
    }

    $conn->close();
    ?>
</body>
</html>
