<?php
    class Response {
        public static function send($success, $code, array $headers, $data) {
            foreach ($headers as $key => $value) {
                header("$key: $value");
            }
            http_response_code($code);

            echo json_encode([
                'success'   => $success,
                'data'      => $data
            ]);
            exit();
        }
    }
?>
