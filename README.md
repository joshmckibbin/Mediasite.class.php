# Mediasite.class.php
A simple PHP class for interacting with the Mediasite API

## Basic Setup
```
// Include the class
require 'Mediasite.class.php';

// Settings
$server = 'https://SERVER.mediasite.com/Mediasite';
$apikey = 'apikey_string';
$username = 'username';
$password = 'password';

// Initialize the class
$ms = new Mediasite($server, $apikey, $username, $password);
```

## Sample Requests
### Create a Presentation & Upload a video to it
```
// Create Presentation
$user = USER_TO_CREATE_THE_PRESENTATION_FOR;
$presentation = $ms->createPresentation('Title', $user);

// Upload Video to Presentation
$video_path = PATH_TO_THE_VIDEO;
$upload = $ms->uploadVideo($presentation['Id'], $video_path);

// Show the URL of the new presentation
echo $upload; 
```
