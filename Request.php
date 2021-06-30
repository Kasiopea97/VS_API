<?php
    class Request {
        private $method;
        private $uri;
        private $data;
        private $files;

        public function __construct(array $server) {
            $this->method = $server['REQUEST_METHOD'];
            $this->uri = $server['REQUEST_URI'];
//            $this->data = json_decode(file_get_contents('php://input'));

            if($server['REQUEST_METHOD'] === 'PUT') {
                parse_str(file_get_contents("php://input"),$post_vars);
                $this->data = $post_vars;
            } else {
                $this->data = $_POST;
            }

            $this->files = $_FILES;
        }

        public function getMethod() {
            return $this->method;
        }

        public function getUri() {
            return $this->uri;
        }

        public function getData() {
            return $this->data;
        }

        public function getFiles($fileName = false) {
            return $fileName ? $this->files[$fileName] : $this->files;
        }

        public function isMethod(string $method) {

            return strcasecmp($this->method, $method) === 0;
        }

        public function isAction(string $action) {
            $regex = '/' . str_replace('\:id', '\d+', preg_quote($action, '/')) . '$/i';

            return preg_match($regex, $this->uri) === 1;
        }

        public function getId() {
            preg_match('/\d+$/', $this->uri, $id);

            return $id[0];
        }
    }
?>
