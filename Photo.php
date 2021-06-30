<?php

include "FileUploader.php";

class Photo {

    protected $user;

    protected $database;

    public function __construct($user, $database)
    {
        $this->user     = $user;
        $this->database = $database;
    }

    public function find($id) {
        try {
            $image = $this->database->query('SELECT * FROM photos WHERE id = ?', $id)->fetchArray();

            if($image) {
                $res = $this->albumBelongsToUser($image['album_id']);

                if($res) {

                    return $image;
                }
            }

            return false;
        } catch (Exception $exception) {

            return false;
        }
    }

    public function store($image, $data) {
        try {
            $res = $this->albumBelongsToUser($data['album_id']);

            if($res) {
                $response = FileUploader::upload($image, $data['album_id']);

                $this->database->query('INSERT INTO photos (album_id, description, location, file_path) VALUES (?,?,?,?)',
                    $data['album_id'],
                    $data['description'],
                    $data['location'],
                    $response['file_path']
                );

                return $this->database->lastInsertID();
            }

            return false;
        } catch (Exception $exception) {

            return false;
        }
    }

    public function update($id, $data) {
        try {
            $res = $this->photoBelongsToUser($id);

            if($res) {
                $this->database->query('UPDATE photos SET description = (?), location = (?), updated_at = (?) WHERE id = (?)', $data['description'], $data['location'], date("Y-m-d H:i:s"), $id);

                return true;
            }

            return false;
        } catch (Exception $exception) {

            return false;
        }
    }

    public function delete($id) {
        try {
            $res = $this->photoBelongsToUser($id);

            if($res) {
                $image = $this->database->query('SELECT * FROM photos WHERE id = ?', $id)->fetchArray();

                if($image && FileUploader::deletePhoto($image['file_path'])) {
                    $this->database->query('DELETE FROM photos WHERE id = (?)', $id);

                    return true;
                }
            }

            return false;
        } catch (Exception $exception) {

            return false;
        }
    }

    public function albumBelongsToUser($album_id)
    {
        if($this->user['is_admin']) {

            return true;
        }

        $album = $this->database->query('SELECT * FROM albums WHERE id = ? and user_id = ?', $album_id, $this->user['id'])->fetchArray();

        return $album;
    }

    public function photoBelongsToUser($photo_id)
    {
        if($this->user['is_admin']) {

            return true;
        }

        $photo = $this->database->query('SELECT * FROM photos WHERE id = ?', $photo_id)->fetchArray();

        if($photo) {
            $res = $this->albumBelongsToUser($photo['album_id']);

            if($res) {
                return true;
            }
        }

        return false;
    }
}