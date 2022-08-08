<?php

namespace Mediasite;

class Mediasite
{

    protected $server = null;
    private $apikey = null;
    private $username = null;
    private $password = null;


    /**
     * Creates the Mediasite Library
     * @param string $server The URL of the Mediasite server
     * @param string $apikey The API key
     * @param string $username The username
     * @param string $password The password
     */
    public function __construct(string $server, string $apikey, string $username, string $password) {

        $this->server = $server;
        $this->apikey = $apikey;
        $this->username = $username;
        $this->password = $password;

    }


    /**
     * Make a CURL Request
     * @param string $endpoint The API endpoint
     * @param array $options Additional CURL options
     * @param array $header The HTTP headers to send
     * @param string $method The request method type (GET, POST, PUT, DELETE)
     * @return array
     */
    public function request(string $endpoint, array $options = array(), array $header = array(), string $method = 'GET') {

        if ( empty($header) ) {
            $header = array(
                'sfapikey: ' . $this->apikey,
                'Authorization: Basic ' . base64_encode($this->username . ":" . $this->password),
                'Content-Type: application/json; charset=utf-8'
            );
        }

        $init_opts = array(
            CURLOPT_URL => $this->server . $endpoint, 
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $header,
        );

        $curl = curl_init();
        curl_setopt_array($curl, $init_opts + $options);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }


    /**
     * Create a Presentation
     * @param string $title The title of the presentation
     * @param string $user The username of the person to create the presentation for
     * @return array
     */
    public function createPresentation(string $title, string $user) {

        $endpoint = '/api/v1/Presentations';

        $data = array(
            'Title' => $title
        );

        $options = array(
            CURLOPT_POSTFIELDS => json_encode($data)
        );

        $header = array(
            'sfapikey: ' . $this->apikey,
            'Authorization: SfIdentTicket ' . base64_encode($this->username . ':' . $this->password . ':' . $user),
            'Content-Type: application/json; charset=utf-8'
        );

        return $this->request($endpoint, $options, $header, 'POST');

    }


    /**
     * Upload Video to Presentation Folder
     * @param string $presentation_id The ID of the Presentation folder to upload to
     * @param string $file_url The URL of the file to upload
     * @param string $filename The filename to give to the video
     * @return array
     */
    public function uploadVideo(string $presentation_id, string $file_url, string $filename = 'video.mp4') {
        
        $endpoint = '/FileServer/Presentation/' . $presentation_id . '/' . $filename;

        $options = array(
            CURLOPT_POSTFIELDS => file_get_contents($file_url)
        );

        $header = array(
            'sfapikey: ' . $this->apikey,
            'Authorization: Basic ' . base64_encode($this->username . ':' . $this->password),
            'Content-Type: video/mp4'
        );

        $this->request($endpoint, $options, $header, 'POST');

        return $this->createMedia($presentation_id, $filename);

    }


    /**
     * Associate Uploaded Video with Presentation
     * @param string $presentation_id The ID of the Presentation folder
     * @param string $filename The filename of the video to associate with the Presentation
     * @return string The URL of the Presentation
     */
    public function createMedia(string $presentation_id, string $filename) {

        $endpoint = '/api/v1/Presentations(\'' . $presentation_id. '\')/CreateMediaUpload';

        $data = array(
            'FileName' => $filename
        );

        $options = array(
            CURLOPT_POSTFIELDS => json_encode($data)
        );

        $media = $this->request($endpoint, $options, array(), 'POST');

        if ($media) {
            return $this->server . '/mymediasite/presentations/' . $presentation_id;
        }

    }


}
