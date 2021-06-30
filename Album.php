<?php

class Album {

    protected $user;

    protected $database;

    public function __construct($user, $database)
    {
        $this->user     = $user;
        $this->database = $database;
    }

    public function getMyAlbums()
    {
        try {
            $query = 'SELECT * from albums';

            if(!$this->user['is_admin']) {
                $query .= " where user_id = {$this->user['id']}";
            }

            $albums = $this->database->query($query)->fetchAll();

            return $albums;
        } catch (Exception $exception) {

            return false;
        }
    }

    public function findAlbumWithPhotos($id) {
        try {
            $query = '
            SELECT
                albums.id as album_id,
                albums.name as album_name,
                photos.id AS photo_id,
                photos.location AS location,
                photos.description AS description,
                photos.file_path AS file_path,
                photos.created_at AS created_at
                
            FROM
                albums
                LEFT JOIN photos ON albums.id = photos.album_id
            WHERE
                albums.id = ?
	';

            if(!$this->user['is_admin']) {
                $query .= " AND albums.user_id = {$this->user['id']}";
            }

            $album = $this->database->query($query, $id)->fetchAll();

            return !empty($album) ? $album : null;
        } catch (Exception $exception) {

            return false;
        }
    }

    public function store($data) {
        try {
            $this->database->query('INSERT INTO albums (name, user_id) VALUES (?,?)', $data['name'], $this->user['id']);

            return $this->database->lastInsertID();
        } catch (Exception $exception) {

            return false;
        }
    }

    public function update($id, $data) {
        try {
            $this->database->query('UPDATE albums SET name = (?), updated_at = (?) WHERE id = (?) AND user_id = (?)', $data['name'], date("Y-m-d H:i:s"), $id, $this->user['id']);

            return true;
        } catch (Exception $exception) {

            return false;
        }
    }

    public function delete($id) {
        try {
            $res = $this->database->query('DELETE FROM albums WHERE id = (?) AND user_id = (?)', $id, $this->user['id']);

            if($this->database->affectedRows() > 0) {
                $this->database->query('DELETE FROM photos WHERE album_id = (?)', $id);

                return true;
            }

            return false;
        } catch (Exception $exception) {

            return false;
        }

    }
}