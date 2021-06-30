<?php
    require_once 'Request.php';
    require_once 'Database.php';
    require_once 'Response.php';
    require_once 'Album.php';
    require_once 'Photo.php';
    require_once 'Auth.php';

    date_default_timezone_set('Europe/Vienna');

    $dbhost = 'localhost'; // 127.0.0.1
    $dbuser = 'user';
    $dbpass = 'root';
    $dbname = 'media_library_api';

    $request = new Request($_SERVER);
    $database = new Database($dbhost, $dbuser, $dbpass, $dbname);
    $auth = new Auth($database);
    $user = null;

    if(!$request->isAction('/api/database_migrate') && !$request->isAction('/api/register') && !$request->isAction('/api/login')) {
        $user = $auth->authenticate();

        if(!$user) {
            Response::send(false,401, [], 'Unauthorized');
        }
    }

    $album = new Album($user, $database);
    $photo = new Photo($user, $database);

    $headers = [
        'Content-Type' => 'application/json; charset=utf-8'
    ];

    if ($request->isMethod('get')) {

        if ($request->isAction('/api/database_migrate')) {
            include('db_script.php');

            Response::send(true, 200, $headers, 'Success');
        }

        if ($request->isAction('/api/albums')) {
            $albums = $album->getMyAlbums();

            Response::send($albums ? true : false, $albums ? 201 : 404, $headers, $albums);
        }

        if ($request->isAction('/api/albums/:id')) {
            $album = $album->findAlbumWithPhotos($request->getId());

            Response::send($album ? true : false, $album ? 201 : 404, $headers, $album);
        }

        if ($request->isAction('/api/photos/:id')) {
            $photo = $photo->find($request->getId());

            Response::send($photo ? true : false, $photo ? 201 : 404, $headers, $photo);
        }

        Response::send(false,501, [], 'unknown action: ' . $request->getUri());
    }

    if ($request->isMethod('post')) {

        if($request->isAction('/api/register')) {
            $res = $auth->register($request->getData());

            Response::send($res ? true : false,$res ? 201 : 400, $headers, $res ? [
                'id'    => $res['id'],
                'token' => $res['token']
            ] : 'Error');
        }

        if($request->isAction('/api/login')) {
            $token = $auth->login($request->getData());

            Response::send($token ? true : false,$token ? 201 : 400, $headers, [
                'token' => $token
            ]);
        }

        if ($request->isAction('/api/albums')) {
            $id = $album->store($request->getData());

            Response::send($id ? true : false,$id ? 201 : 400, $headers, [
                'id'    => $id
            ]);
        }

        if ($request->isAction('/api/photos')) {
            $id = $photo->store($request->getFiles('image'), $request->getData());

            Response::send($id ? true : false,$id ? 201 : 400, $headers, [
                'id'    => $id
            ]);
        }

        Response::send(false, 501, [], 'unknown action: ' . $request->getUri());
    }

    if ($request->isMethod('put')) {

        if ($request->isAction('/api/albums/:id')) {

            $updated = $album->update($request->getId(), $request->getData());

            Response::send($updated ? true : false, 200, [], null);
        }

        if ($request->isAction('/api/photos/:id')) {

            $updated = $photo->update($request->getId(), $request->getData());

            Response::send($updated ? true : false, 200, [], null);
        }

        Response::send(false, 501, [], 'unknown action: ' . $request->getUri());
    }

    if ($request->isMethod('delete')) {

        if ($request->isAction('/api/albums/:id')) {

            $deleted = $album->delete($request->getId());

            Response::send($deleted ? true : false, 200, [], null);
        }

        if ($request->isAction('/api/photos/:id')) {

            $deleted = $photo->delete($request->getId());

            Response::send($deleted ? true : false, 200, [], null);
        }


        Response::send(false, 501, [], 'unknown action: ' . $request->getUri());
    }

    Response::send(false, 501, [], 'unknown action: ' . $request->getMethod());
?>
