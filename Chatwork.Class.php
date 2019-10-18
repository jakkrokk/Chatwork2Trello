<?php
Class Chatwork {
    private $endPoint = 'https://api.chatwork.com/v2/my/tasks';
    private $token = 'api token here'; //Api token.

    public function __construct() {
    }

    public function getFromApi() {
        $c = stream_context_create(array(
                'http' => [
                    'ignore_errors' => true,
                    'header'  => implode("\r\n", ["X-ChatWorkToken: {$this->token}"])
                ]
        ));
        $api = file_get_contents($this->endPoint,false,$c);
        return json_decode($api);
    }

}
