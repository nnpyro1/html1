<?php
//------------------------------------------------------------
//联接sql
$sql_name = "root";
$sql_pwd = "00000000";
$sql_dbname = "chat";
//创建联接
$connection = new mysqli("localhost",$sql_name,$sql_pwd,$sql_dbname);
//错误处理
if($connection->connect_error){
    $output = [
        "error" => "Cannot connect to server.",
    ];
    echo json_encode($output);
    exit();
}


//输入处理
$user_id = $_POST['id'];
$user_name = $_POST['name'];
$user_pwd = $_POST['pwd'];
$message = $_POST['message'];
$time = $_POST['time'];
$action = $_POST['action'];
$no = $_POST['no'];
// echo json_encode($_POST);

if(!$user_id || !$user_name){
    $output = [
        "error" => "Fromat error.",
    ];
    echo json_encode($output);
    exit();
}

// echo "$user_id --- $user_name ;<br>";
$result = $connection->query("SELECT * FROM users WHERE id=$user_id AND username='$user_name' AND pwd='$user_pwd'");
if($result->num_rows == 0){
    $output = [
        "error" => "User error.",
    ];
    echo json_encode($output);
    exit();
}

// if($action == "readall"){
//     // $output = ["messages" => []];
//     // $result = $connection->query("SELECT * FROM messages");
//     // $l = 1;
//     // while($row = $result->fetch_assoc()){
//     //     // echo "$row[user_id] $row[user_name] $row[message] $row[time]";
//     //     echo $l;
//     //     $output['messages'][$l] = [
//     //         "user_id" => $row['user_id'],
//     //         "user_name" => $row['user_name'],
//     //         "message" => $row['message'],
//     //         "time" => $row['time'],
//     //     ];
//     //     $l = $l + 1;
//     // }
//         $result = $connection->query("SELECT * FROM messages");

// // 构建结果数组
// $output = ["messages" => []];
// while($row = $result->fetch_assoc()) {
//     $output['messages'][] = [
//         "user_id"   => $row['user_id'],
//         "user_name" => $row['user_name'],
//         "message"   => $row['message'],
//         "time"      => $row['time']
//     ];
// }
//     echo json_encode($row,JSON_PRETTY_PRINT);
// }
/*if($action == "read"){
    $result = $connection->query("SELECT * FROM messages");
    $i = 0;
    $flag = false;
    while($row = $result->fetch_assoc()){
        if($i == $no){
            $output = [
                "user_id"   => $row['user_id'],
                "user_name" => $row['user_name'],
                "message"   => $row['message'],
                "time"      => $row['time']
            ];
            $flag = true;
            echo json_encode($output);
            exit();
        }
    }
    if($flag == false){
        $output = [
            "error" => "Not found",
        ];
        echo json_encode($output);
        exit();
    }
}*/

//读取操作
if($action == "read"){
    $result = $connection->query("SELECT * FROM messages ORDER BY sort LIMIT 1 OFFSET ".($no-1));
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $output = [
            "user_id"   => $row['user_id'],
            "user_name" => $row['user_name'],
            "message"   => $row['message'],
            "time"      => $row['time']
        ];
        echo json_encode($output);
        exit();
    }
    else{
        $output = [
            "error" => "Not found",
        ];
        echo json_encode($output);
        exit();
    }
}

//写入操作
if($action == "send text"){
    // echo "$message<br>$time<br>";
    if($connection->query("INSERT INTO messages VALUES ($user_id,'$user_name','$message','$time',NULL)")){
        $output = [
            "state" => "Successfully",
        ];
        echo json_encode($output);
    }
    else{
        $output = [
            "error" => "Cannot insert :  $sql   : ".mysqli_error($connection),
        ];
        echo json_encode($output);
    }
    exit();
}

//读取长度
if($action == "size"){
    $result = $connection->query("SELECT * from messages");
    echo json_encode(["size" => $result->num_rows]);
}

//读取合成文本
if($action == "read list"){
    $result = $connection->query("SELECT * FROM messages");
    $output = "";
    while($row = $result->fetch_assoc()){
        $output = $output . "[" . $row["user_name"] . "]:" . $row["message"].  "\n"; 
    }
    echo $output;
}
?>