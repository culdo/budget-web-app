<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>雲記帳</title>
</head>
<body>
    <div class="top">
        <form action="demo.php" style="text-align: center;">
            📆 <input type="date" name="date" style="width: 95px;">
            <input list="kind" placeholder="蝦款..." name="kind" style="width: 60px;"><br>
            <datalist id="kind">
                <option value="早午餐">
                <option value="晚餐">
            </datalist>
            🛒 <input type="text" placeholder="吃啥買啥...🌭🥪🍺" name="name" id="name">
            <input type="text" placeholder="備註..." name="info" id="info">📝<br>
            🤳 <input type="text" placeholder="沒圖沒真相..." name="image" id="image">
            <input type="text" placeholder="噴多少錢..." name="cost" id="cost">💰<br>
            <button type="submit" name="op_type" value="search">🔍 搜尋</button>
            <button type="submit" name="op_type" value="insert" onClick="return check_form();">💸 新增花費</button>
            <button type="submit" name="op_type" value="plszz">🎲 要吃啥</button>
        </form>
    </div>

    <div class="content">
        <?php 
            ini_set('display_errors', 0);
            $conn = new mysqli("localhost", "root", "0000", "final_term_demo");
            $conn->query("SET NAMES utf8");
            $instr="select * from lose_money order by date desc, id desc";

            if(!empty($_GET['op_type'])) {
                $op_type = $_GET['op_type'];
                $instr = null;
                
                if($op_type=="search") {
                    $instr = "select * from lose_money";

                    $is_first_q = true;
                    foreach ($_GET as $param=>$value) {
                        if($param!="op_type" and $value!=null) {
                            $param = $conn->escape_string($param);
                            $value = $conn->escape_string($value);
                            if($is_first_q) {
                                $instr .= " where ";
                            }else {
                                $instr .= " and ";
                            }
                            $instr .= $param;
                            if($param=="date") {
                                $instr .= " like '$value%'";
                            } else {
                                $instr .= " like '%$value%'";
                            }

                            if($is_first_q == true) {
                                $is_first_q = false;
                            }
                        }
                    }
                    $instr .= " order by date desc, id desc";
                    
                } elseif($op_type=="insert") {
                    if(!empty($_GET["cost"])) {
                        $query = array();
                        foreach ($_GET as $param=>$value) {
                            if($param!="op_type" and $value!=null) {
                                $param = $conn->escape_string($param);
                                $value = $conn->escape_string($value);
                                $query[$param] = "'$value'";
                            }
                        }
                        $ps = implode(",", array_keys($query));
                        $vs = implode(",", array_values($query));
                        $instr = "insert into lose_money ($ps) values ($vs)";
                    } else {
                        echo "你沒噴錢新增三小喇。";
                    }
                } elseif($op_type=="plszz") {
                    if(!empty($_GET["kind"])) {
                        $kind = $_GET["kind"];
                        $kind = $conn->escape_string($kind);
                        $instr = "select * from lose_money where kind = '$kind' order by rand() limit 1 ";
                    } else {
                        $meals = array('早午餐','早餐','午餐','晚餐');
                        $kind = rand(0, 3);
                        // if($kind<=1) $kind = 0; else $kind -= 1;
                        $kind = $meals[$kind];
                        $instr = "select * from lose_money where kind in ('$kind') order by rand() limit 1 ";
                    }
                }elseif($op_type=="delete" and !empty($_GET["id"])) {
                    $del_id = $conn->escape_string($_GET["id"]);
                    $instr = "delete from lose_money where id=$del_id";
                } else {
                    echo "我Get不到你想做啥就丟全部";
                }
            }
            
                
            if($instr!=null) {
                // echo $instr."<br>";
                $result=$conn->query($instr);
                
                if($result===true) {
                    if($op_type=="insert" or $op_type=="delete") {
                        if($op_type=="insert") {
                            $last_id = $conn->insert_id;
                        }
                        $result=$conn->query("select * from lose_money order by date desc, id desc");
                    }
                }
                if ($result->num_rows > 0) {
                    // output data of each row
                    while($row = $result->fetch_assoc()) {
                        $date = explode(" ", $row["date"])[0];
                        $name = $row["name"];
                        $kind = $row["kind"];
                        $cost = $row["cost"];
                        $info = $row["info"];
                        $id = $row["id"];
                        $weekday = '星期' . ['日', '一', '二', '三', '四', '五', '六'][date("w", strtotime($date))];
                        
                        if($op_type=="delete" and $id==$_GET["next_id"]) {
                            echo "<h2 id='deleted' style='text-align: center;'>成功刪除此花費<a href='#$del_id'>#$del_id</a></h2>";
                        }

                        echo "<div id='$id' class='card'>
                                <div class='container'><div>";
                        if($op_type=="insert" and $id==$last_id) {
                            echo "<span style='color:red; border: 2px red solid;margin-right:5px;'><b>新加入</b></span>";
                        }
                        echo "<a href='#$id'>#$id</a><button class='btn-x' onclick='drop_item(this, $id);'>🞩</button>
                        </div>
                        <h3>
                            <b id='ed-name' contenteditable=true>$name</b>
                            <span style='float:right;'>噴<span id='ed-cost' contenteditable=true>$cost</span>元</span>
                        </h3>
                                <p style='white-space:pre;'><span id='ed-kind' contenteditable=true>$kind</span><span>  <span id='ed-info' contenteditable=true>$info</span><span><span style='float:right;'><span id='ed-date' contenteditable=true>$date</span><span>  $weekday</span></p> 
                            </div>";
                        if($row["image"]!=null) {
                            $image = $row["image"];
                            echo "<img src='$image' alt='Avatar' style='width:100%'>";
                        }
                        echo "</div>";
                    }
                } 
            } 

            $conn->close();
        ?>
    </div>

    <script>
        <?php
            if(!empty($last_id)) {
                echo "window.onload = function(){location.href='#$last_id';}\n";
            }
        ?>
        function check_form() {
        }
        function drop_item(bt, id) {
            let next_el = bt.parentNode.parentNode.parentNode.nextElementSibling;
            if(next_el.id=="deleted") {
                next_el = next_el.nextElementSibling;
            }
            let next_id = next_el.id;
            location.href=`demo.php?op_type=delete&id=${id}&next_id=${next_id}#deleted`;
        }
    </script>
    <style>
        .card {
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            transition: 0.3s;
            width: 420px;
            border-radius: 5px;
            margin: 18px auto;
            background-color: beige;
        }

        .card:hover {
            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
        }

        img {
            border-radius: 0 0 5px 5px;
        }

        .container {
            position:relative;
            padding: 2px 16px;
        }
        input[type="date"]::-webkit-clear-button {
            display: none;
        }

        /* Removes the spin button */
        input[type="date"]::-webkit-inner-spin-button { 
            display: none;
        }
        /* .top {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: white;
            padding-bottom: 5px;
            border-bottom: 1px grey solid;
        }
        .top + .content {
            padding-top: 110px;
        } */
        .top {
            padding-bottom: 5px;
            margin-bottom:5px;
            border-bottom: 1px grey solid;
        }
        .btn-x {
            float:right;
            position:relative;
            right:-10px;
            background-color:#d9534f;
            color:white;    
            margin:5px 0;

            border: none;
            width: 30px;
            height: 30px;
            padding: 6px 0px;
            border-radius: 15px;
            text-align: center;
            font-size: 12px;
            box-shadow: 2px 0 4px 0 rgba(0,0,0,0.2);
            transition: 0.3s;
        }
        .btn-x:hover {
            box-shadow: 4px 0 8px 0 rgba(0,0,0,0.2);
        }
        h3 {
            clear: both;
        }
    </style>

</body>
</html>