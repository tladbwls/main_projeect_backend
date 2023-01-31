<?php
    // include $_SERVER['DOCUMENT_ROOT'].'/main)backend/etc/error/php';

    include_once $_SERVER['DOCUMENT_ROOT'].'/soaply_backend/connect/dbconn.php';

    if($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['req_sign'] == "signup") {
        signup($conn);
    }

    if($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['req_sign'] == "signin") {
        signin($conn);
    }

    if($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['q']) && $_GET['q'] == "signout") {
        signout();
    }

    

    function signup ($conn) { //회원가입 처리 함수
        // 포스트 변수 할당
        $name = $_POST['name'];
        $id = $_POST['id'];
        $email = $_POST['email'];
        $pwd = $_POST['pwd'];
        $lvl = 9;

        $pwd = password_hash($pwd, PASSWORD_DEFAULT);

        // echo json_encode(array("name" => var_dump($conn)));

        //sql 입력 명령어 작성
        $sql = "INSERT INTO spl_user (user_name, user_id, user_email, user_pass, user_lvl) VALUES (?,?,?,?,?)";

        //stmt init 참조 : https://www.w3schools.com/php/func_mysqli_stmt_init.asp
        $stmt = $conn->stmt_init();

        if (!$stmt->prepare($sql)) {
          http_response_code(400);
          echo json_encode(array("err message" => "Database insert fail."));
        } 

        $stmt -> bind_param("sssss", $name, $id, $email, $pwd, $lvl);
        $stmt -> execute();

        if ($stmt->affected_rows > 0 ) {
            http_response_code(200);
            echo json_encode(array("msg" => "sign up successfully!"));
        } else {
            http_response_code(400);
            echo json_encode(array("msg" => "Sign up fail"));
        }

        // echo json_encode(array("name" => var_dump($stmt)));

        // echo json_encode(array("name" => $name, "id" => $id, "email" => $email, "pwd" => $pwd)); //문자열을 json 배열 변수로 반환한다. 파라미터에는 반드시 배열이 있어야 한다.
    }
    
    function signin($conn){ //로그인 처리 함수

        //로그인 로직
        //1. 받아온 아이디와 데이터베이스에 존재하는 아이디 비교
        //2. 아이디가 없으면 없다는 메시지 전달
        //3. 아이디가 존재하면 비밀번호와 데이터베이스에 존재하는 비밀번호 비교
        //4. 비밀번호가 일치하지 않으면 없는 비번 메시지 전달
        //5. 비밀번호가 일치하면 필요한 값 세션 저장
        


        $userid = $_POST['id'];
        $pwd = $_POST ['pwd'];

        $sql = "SELECT * FROM spl_user WHERE user_id = ?";
        $stmt = $conn->stmt_init();

        $stmt->prepare($sql);
        $stmt->bind_param('s',$userid);
        $stmt->execute();

        $result = $stmt->get_result();

        if (!mysqli_num_rows($result)) {
            echo json_encode(array("login_msg" => "존재하지 않는 아이디입니다."));
        } else {
            $login_data = $result->fetch_array(); //회원 데이터 전체 추출하여 저장

            $pwd_valid = password_verify($pwd, $login_data['user_pass']); //입력된 데이터(첫번째 파라미터)와 디비의 해시 데이터(두번째 파라미터)를 비교하여 boollean 값으로 변함

            if (!$pwd_valid) {
                echo json_encode(array("login_msg" => "비밀번호가 틀립니다."));
            } else {
                
                $_SESSION['userid'] = $userid;
                $_SESSION['useridx'] = $login_data['user_idx'];
                $_SESSION['userlvl'] = $login_data['user_lvl'];
                echo json_encode(array("userid" => $_SESSION['userid'], "useridx" => $_SESSION['useridx'], "userlvl" => $_SESSION['userlvl']));
            }
        }
    } //로그인 처리 함수

    function signout(){
        if (isset($_SESSION['userid'])) {
           session_unset();
           session_destroy();
           echo json_encode(array("userid"=>"guest"));
        } 
        
    } //로그아웃 처리 함수

?>