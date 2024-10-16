<?php
namespace App\Common\Service;


class ResponseMessage {
  public function response($success, $code, $message, $data = null) {
    if($code != '00') {
      return [
        'success' => $success,
        'cod_error' => $code,
        'message_error' => $message
      ];
    }
    
    return [
      'success' => $success,
      'cod_error' => $code,
      'message' => $message,
      'data' => $data
    ];
  }
}