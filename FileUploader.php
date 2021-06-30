<?php

class FileUploader {

    public static function upload($fileToUpload, $album_id) {
//        $fileToUpload = $_FILES["fileToUpload"];

        if (!file_exists('../uploads')) {
            mkdir('../uploads', 0777, true);
        }

        if (!file_exists("../uploads/{$album_id}")) {
            mkdir("../uploads/{$album_id}", 0777, true);
        }

        $target_dir = "../uploads/{$album_id}/";
        $target_file = $target_dir . time() . '-' . basename($fileToUpload["name"]);

        $response = [
            'success'   => true,
            'message'   => '',
            'file_path' => ''
        ];

        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($fileToUpload["tmp_name"]);
        if($check == false) {
            $response = [
                'success'   => false,
                'message'   => 'File is not an image'
            ];
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $response = [
                'success'   => false,
                'message'   => 'Only jpg,png, and jpeg formats are supported'
            ];
        }

        // Check if $uploadOk is set to 0 by an error
        if ($response['success']) {
            if (move_uploaded_file($fileToUpload["tmp_name"], $target_file)) {
                $response['message'] = 'Success';
                $response['file_path'] = $target_file;
            } else {
                $response = [
                    'success'   => false,
                    'message'   => 'Error uploading image'
                ];
            }
        }

        return $response;
    }

    public static function deletePhoto($file_path) {
        try {
            unlink($file_path);

            return true;
        } catch (Exception $exception) {

            return false;
        }
    }
}