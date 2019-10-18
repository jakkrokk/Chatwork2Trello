<?php
Class InsertTaskToTrello {

    private $exp = [
        "/\[qt\]/ius",
        "/\[\/qt\]/ius",
        "/\[qtmeta.*?\]/ius",
        "/\[To\:.*?\].*?さん/ius",
        "/\[download\:.*?\]/ius",
        "/\[\/download\]/ius",
        "/\[dtext\:file_uploaded\]/ius",
        "/\[title\]/ius",
        "/\[\/title\]/ius",
        "/\[info\]/ius",
        "/\[\/info\]/ius",
        "/\[preview.*?\]/ius",
        "/\[rp.*?\]/ius"
    ];

    public function __construct() {
    }


    public function execute() {
        $CW = new Chatwork();
        $TL = new Trello();

        $tasks = $CW->getFromApi();

        $listList= [];
        $taskList= [];
        foreach ($tasks as $v) {
            $str = trim(preg_replace($this->exp,'',$v->body));
            $name = preg_split("/\n/ius",$str,-1,PREG_SPLIT_NO_EMPTY);
            $listList[$v->room->name] = 1;
            $name = trim($name[0])."({$v->task_id})";
            $taskList[$name] = ['listId'=>$v->room->name,'name'=>$name,'desc'=>"{$str}\n\nチャット名:{$v->room->name}\nタスクID:{$v->task_id}\nステータス:{$v->status}\n\n"];
        }

        $lists = $TL->getLists();
        $tasks = $TL->getCards();
        
        $existsListList= [];
        foreach ($lists as $v) {
            $existsListList[$v->name] = $v->id;
        }

        $existsTaskList= [];
        foreach ($tasks as $v) {
            $existsTaskList[$v->name] = $v;
        }

        foreach ($listList as $listName=>$v) {
            if (!isset($existsListList[$listName])) {
                $tmp = $TL->createList($listName);
                $existsListList[$tmp->name] = $tmp->id;
            }
        }

        foreach ($taskList as $name=>$task) {
            if (!isset($existsTaskList[$task['name']])) {
                $listId = $existsListList[$task['listId']];
                $tmp = $TL->createCards($listId,$task['name'],$task['desc']);
                echo "追加しました：[{$task['name']}]\n";
            } else {
                if ($existsTaskList[$task['name']]->name.$existsTaskList[$task['name']]->desc !== $task['name'].$task['desc']) {
                    $TL->updateCards($existsTaskList[$task['name']]->id,$task['name'],$task['desc']);
                    echo "更新しました：[{$task['name']}]\n";
                } else {
                    echo "更新なし：[{$task['name']}]\n";
                }
            }
        }

        foreach ($existsTaskList as $name=>$existedTask) {
            if (!isset($taskList[$name])) {
                $name = str_replace("[削除されました]","",$name);
                $TL->updateCards($existedTask->id,"[削除されました]{$name}",$existedTask->desc);
                echo "削除しました：[{$existedTask->name}]\n";
            }
        }
    }
}