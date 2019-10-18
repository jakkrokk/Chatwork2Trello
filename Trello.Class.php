<?php
Class Trello {
    private $boardId = 'target board id here'; // Target Board id.
    private $key = 'api key here'; // Api key.
    private $token = 'api token here'; // Api token.

    private $endPointLists = 'https://trello.com/1/boards/__BOARDID__/lists?key=__KEY__&token=__TOKEN__&fields=name';
    private $endPointCards = 'https://trello.com/1/boards/__BOARDID__/cards?key=__KEY__&token=__TOKEN__';
    private $endPointCreateLists = 'https://trello.com/1/lists/?key=__KEY__&token=__TOKEN__&idBoard=__BOARDID__';
    private $endPointCreateCards = 'https://trello.com/1/cards/?key=__KEY__&token=__TOKEN__&idList=__LISTID__';
    private $endPointUpdateCards = 'https://trello.com/1/cards/__CARDID__?key=__KEY__&token=__TOKEN__';

    public function __construct() {
    }

    public function getLists() {
        $api = file_get_contents(str_replace(['__BOARDID__','__KEY__','__TOKEN__'],[$this->boardId,$this->key,$this->token],$this->endPointLists));
        $this->json = json_decode($api);
        //var_dump($this->json);
        return $this->json;
    }

    public function getCards() {
        $api = file_get_contents(str_replace(['__BOARDID__','__KEY__','__TOKEN__'],[$this->boardId,$this->key,$this->token],$this->endPointCards));
        $this->json = json_decode($api);
        //var_dump($this->json);
        return $this->json;
    }

    public function createList($boardTitle) {
        $stream = $this->createStream('POST',['name'=>$boardTitle]);
        $api = file_get_contents(str_replace(['__BOARDID__','__KEY__','__TOKEN__'],[$this->boardId,$this->key,$this->token],$this->endPointCreateLists),false,stream_context_create($stream));
        $this->json = json_decode($api);
        //var_dump($this->json);
        return $this->json;
    }

    public function createCards($listId,$name,$desc) {
        $stream = $this->createStream('POST',['name'=>$name,'desc'=>$desc]);
        $api = file_get_contents(str_replace(['__LISTID__','__KEY__','__TOKEN__'],[$listId,$this->key,$this->token],$this->endPointCreateCards),false,stream_context_create($stream));
        $this->json = json_decode($api);
        //var_dump($this->json);
        return $this->json;
    }

    public function updateCards($cardId,$name,$desc) {
        $stream = $this->createStream('PUT',['name'=>$name,'desc'=>$desc]);
        $api = file_get_contents(str_replace(['__CARDID__','__KEY__','__TOKEN__'],[$cardId,$this->key,$this->token],$this->endPointUpdateCards),false,stream_context_create($stream));
        $this->json = json_decode($api);
        //var_dump($this->json);
        return $this->json;
    }

    private function createStream($method,$data){
        $data = http_build_query($data, "", "&");
        // header
        $header = [
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Length: ".strlen($data)
        ];
        return [
            "http" => [
                "method"  => $method,
                "header"  => implode("\r\n",$header),
                "content" => $data
            ]
        ];
    }
}
