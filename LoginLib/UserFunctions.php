<?php

//TODO add include from the other classes.

require_once 'Database.php';
require_once 'Authentication.php';

class UserFunctions{

//Unsure of whether these are really necessary if we do use includes.
private  var $dbConnection;
private var $currentUser;

public function __construct(){
  $this->$dbConnection = Database::getDBConnection();
  $this->$currentUser = Authentication::getCurrentUser();
}

//Unsure if we are using the previous user table or making a new one. Clarification from group needed.
public function editFirstName($newFirstName){
  if($currentUser->isValidName($newFirstName)){
    $sql = "UPDATE tb_users SET firstname = :firstname WHERE user_id = :user_id";
    $statement = $this->$dbConnection->prepare($sql);
    $statement->bindParam(":firstname", $newFirstName, PDO::PARAM_STR);
    $statement->bindParam(":user_id", $currentUser->getUserId, PDO::PARAM_INT);
    $statement->execute();
    return true;
  }else {
    return false;
  }
}

public function editLastName($newLastName){
  if($currentUser->isValidName($newLastName)){
    $sql = "UPDATE tb_users SET lastname = :lastname WHERE user_id = :user_id";
    $statement = $this->$dbConnection->prepare($sql);
    $statement->bindParam(":lastname", $newLastName, PDO::PARAM_STR);
    $statement->bindParam(":user_id", $currentUser->getUserId, PDO::PARAM_INT);
    $statement->execute();
    return true;
  }else {
    return false;
  }
}

public function editMiddleName($newMiddleName){
  if($currentUser->isValidName($newMiddleName)){
    $sql = "UPDATE tb_users SET middlename = :middlename WHERE user_id = :user_id";
    $statement = $this->$dbConnection->prepare($sql);
    $statement->bindParam(":middlename", $newMiddleName, PDO::PARAM_STR);
    $statement->bindParam(":user_id", $currentUser->getUserId, PDO::PARAM_INT);
    $statement->execute();
    return true;
  }else {
    return false;
  }
}

public function editEmail($newEmail){
  if(filter_var($newEmail, FILTER_VALIDATE_EMAIL)){
    $sql = "UPDATE tb_users SET email = :email WHERE user_id = :user_id";
    $statement = $this->$dbConnection->prepare($sql);
    $statement->bindParam(":email", $newEmails, PDO::PARAM_STR);
    $statement->bindParam(":user_id", $currentUser->getUserId, PDO::PARAM_INT);
    $statement->execute();
    return true;
  }else {
    return false;
  }
}

}
?>