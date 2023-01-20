<?php

// 1. 카트 저장 2. 카트 요청 3. 카트 삭제

$req_cart = $_GET['req_cart'];

session_start();
// 1. 카트 저장
//- 처음 하나의 상품을 입력할 경우 새션 생성
//2. 세션의 갯수를 생성 인덱스로 지정하여 또 하나의 세션 생성
if ($req_cart == "add_cart") {
  if (isset($_SESSION['cart'])) {
    $addedItem = array_column($_SESSION['cart'], 'cart_name'); //주어진 배열(첫번째 파라미터)에서 특정 칼럼(두번째 파라미터)값 반환 = https://zetawiki.com/wiki/PHP_array_column()

    if (in_array($_POST['cart_name'], $addedItem)) { //첫번째 파라미터 배열에 두번째 파라미터 값이 있으면 true 없으면 false
      echo json_encode(array("msg" => "이미 추가된 상품입니다."));
      
    } else {
      //2. 세션의 갯수를 생성 인덱스로 지정하여 또 하나의 세션 생성
      $count = count($_SESSION['cart']);
      $_SESSION['cart'][$count] = array(
        'cart_idx' => $_POST['cart_idx'],
        'cart_name' => $_POST['cart_name'],
        'cart_desc' => $_POST['cart_desc'],
        'cart_pri' => $_POST['cart_pri'],
        'cart_img' => $_POST['cart_img'],
        'cart_count' => $_POST['cart_count'],
        'cart_sum' => $_POST['cart_sum'],
      );
      echo json_encode(array("msg" => " 카트에 상품이 추가되었습니다."));

    }
  } else {
    //세션이 존재하지 않을 때 첫 세션 생성
    
    $_SESSION['cart'][0] = array(
      'cart_idx' => $_POST['cart_idx'],
      'cart_name' => $_POST['cart_name'],
      'cart_desc' => $_POST['cart_desc'],
      'cart_pri' => $_POST['cart_pri'],
      'cart_img' => $_POST['cart_img'],
      'cart_count' => $_POST['cart_count'],
      'cart_sum' => $_POST['cart_sum'],
    );
    echo json_encode(array("msg" => " 카트에 상품이 추가되었습니다."));

  }
  


}

//2.카트 요청 
if ($req_cart == "get_cart") {
  if (isset($_SESSION['cart'])) {
    $cart_lists = $_SESSION['cart'];
  } else {
    $cart_lists = null;
  }
  echo json_encode($cart_lists);
  
}

//3. 카트 삭제 
if ($req_cart == "del_cart") {
  $cart_idx = $_GET['cart_idx'];
    foreach($_SESSION['cart'] as $key => $value) {
      if ($value['cart_idx'] == $cart_idx) {
        unset($_SESSION['cart'][$key]); //삭제되는 세션의 인덱스 
        //array value 문 : https://www.php.net/manual/en/function.array-values.php  //삭제된 세션의 인덱스를 재배치
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        echo json_encode(array("msg" => "카트에서 상품이 삭제되었습니다."));

      }
    }

}



?>