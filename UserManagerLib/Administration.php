<?php

require_once 'Database.php';
require_once 'User.php';
require_once "HelperFunctions.php";
require_once "Registration.php";

/**
 * Created by PhpStorm.
 * User: Benjamin Arnold <benji.arnold@gmail.com>
 * Date: 11/11/2016
 * Time: 2:53 PM
 */
class Administration
{
    //setting up prepared statement placeholders to make my sql quires dynamic.
    const PREPARED_STATEMENT_1 = ":prepared_1";
    const PREPARED_STATEMENT_2 = ":prepared_2";


    public static function createUser($firstName, $middleName, $lastName, $email, $site, $password, $authType = "REGULAR")
    {
        if (HelperFunctions::isLoggedIn() && HelperFunctions::isUserAuthorized(1)) {
            return Registration::registerNewUser($firstName, $middleName, $lastName, $email, $site, $password, $authType);
        }
    }


    public static function editUserType($email, $authType)
    {
        $sql = "UPDATE users SET auth_type = " . self::PREPARED_STATEMENT_1 . " WHERE email = " . self::PREPARED_STATEMENT_2;
        self::runSQLWithTwoClauses($sql, $email, $authType, false);
    }

    /**
     * Adds website registration to user
     */
    public static function editUserAddRegistrationTo($userId, $websiteId)
    {
        $sql = "INSERT INTO user_site_xref (user_id, site_id) VALUES (" . self::PREPARED_STATEMENT_1 . ", " . self::PREPARED_STATEMENT_2 . ");";
        $results = self::runSQLWithTwoClauses($sql, $userId, $websiteId, true);
        return $results;
    }

    /**
     * Removes website registration from user
     * @param $userId
     * @param $websiteId
     */
    public static function editUserRemoveRegistrationFrom($userId, $websiteId)
    {
        $sql = "DELETE FROM user_site_xref WHERE user_id = " . self::PREPARED_STATEMENT_1 . " AND site_id = " . self::PREPARED_STATEMENT_2;
        $results = self::runSQLWithTwoClauses($sql, $userId, $websiteId, true);
        return $results;
    }

    /**
     * Select lastname, firstname, middlename, email from user with user_id
     * @param $userId
     */
    public static function getUserWithID($userId)
    {
        $sql = "SELECT lastname, firstname, middlename, email  FROM users WHERE " .
            "user_id = " . self::PREPARED_STATEMENT_1 . " ORDER BY lastname";
        $results = self::runSQLWithOneClause($sql, $userId, true);
        return $results;
    }

    /**
     * Select lastname, firstname, middlename, user_id from user with email
     * @param $userEmail
     */
    public static function getUserWithEmail($userEmail)
    {
        $sql = "SELECT lastname, firstname, middlename, user_id FROM users WHERE " .
            "email = " . self::PREPARED_STATEMENT_1 . " ORDER BY lastname";
        $results = self::runSQLWithOneClause($sql, $userEmail, true);
        return $results;
    }

    /**
     * Select lastname, firstname, middlename, email, user_id from user with passed firstname and lastname
     * @param $userFirstName
     * @param $userLastName
     */
    public static function getUsersWithFirstAndLastName($userFirstName, $userLastName)
    {
        $sql = "SELECT lastname, firstname, middlename, email, user_id FROM users WHERE " .
            " firstname = " . self::PREPARED_STATEMENT_1 . " AND lastname = " . self::PREPARED_STATEMENT_2 . " ORDER BY lastname";
        $results = self::runSQLWithTwoClauses($sql, $userFirstName, $userLastName, true);
        return $results;
    }

    /**
     * Select all users in ascending order whose first name starts with passed letter
     * @param $searchLetter
     */
    public static function getUsersByFirstNameStartingWith($searchLetter)
    {
        $sql = "SELECT firstname, middlename, lastname, email, user_id FROM users WHERE " .
            "firstname LIKE " . self::PREPARED_STATEMENT_1 . "% ORDER BY firstname ASC";
        $results = self::runSQLWithOneClause($sql, $searchLetter, true);
        return $results;
    }

    /**
     * Select all users in ascending order whose last name starts with passed letter
     * @param $searchLetter
     */
    public static function getUsersByLastNameStartingWith($searchLetter)
    {
        $sql = "SELECT lastname, firstname, middlename, email, user_id FROM users WHERE lastname LIKE " . self::PREPARED_STATEMENT_1 . "% ORDER BY lastname ASC";
        $results = self::runSQLWithOneClause($sql, $searchLetter, true);
        return $results;
    }

    /**
     * Select all users who are registered to the passed website name
     * @param $currentWebsite
     */
    public static function getUsersRegisteredToThisSite($currentWebsite)
    {
        $sql = "SELECT users.lastname, users.firstname, users.middlename, users.email, users.user_id FROM " .
            "users INNER JOIN users ON users.user_id = user_site_xref.user_id INNER JOIN sites ON " .
            "sites.site_id = user_site_xref.site_id WHERE site.site_name = " . self::PREPARED_STATEMENT_1 . " ORDER BY lastname";
        $results = self::runSQLWithOneClause($sql, $currentWebsite, true);
        return $results;
    }

    /**
     * Select all users with passed type.  Unused, field not present in database
     * @param $userType
     */
    public static function getUsersWithType($userType)
    {
        $sql = "SELECT users.lastname, users.firstname, users.middlename, users.email, users.user_id FROM " .
            "users INNER JOIN users ON users.user_id = user_auth_xref.user_id INNER JOIN auth ON " .
            "auth.auth_type = user_auth_xref.auth_type WHERE auth.auth_type = " . self::PREPARED_STATEMENT_1 . " ORDER BY lastname";
        $results = self::runSQLWithOneClause($sql, $userType, true);
        return $results;
    }

    /**
     * Get a list of all websites and their associated ids
     */
    public static function getWebsites()
    {
        $sql = "SELECT * FROM sites";
        $results = self::runSQLWithNoClause($sql, true);
        return $results;
    }

    /**
     * Deletes user with passed user id
     * @param $userId
     */
    public static function deleteUserById($userId)
    {
        $sql = "DELETE FROM users WHERE user_id = " . self::PREPARED_STATEMENT_1 . "";
        self::runSQLWithOneClause($sql, $userId, false);
    }


    public static function addNewAuthType($newAuthType, $rankAssociated)
    {
        $isAuthTypeUnique = !HelperFunctions::isValidType($newAuthType);
        $isCharsAllUpperCase =  ctype_upper($newAuthType);
        $isRankAnInt = ctype_digit($rankAssociated);

        if($isAuthTypeUnique && $isCharsAllUpperCase && $isRankAnInt && HelperFunctions::isUserAuthorized(0)) {

            $sql = "INSERT INTO auth (auth_type, auth_rank) values( " . self::PREPARED_STATEMENT_1 . ", " . self::PREPARED_STATEMENT_2 . ");";
            self::runSQLWithTwoClauses($sql, $newAuthType, $rankAssociated, false);
        }

    }

    public static function deleteAuthType($authType){
        if (HelperFunctions::isUserAuthorized(0)) {
            if($authType != "SUPER_ADMIN" || $authType != "ADMIN") {

                $sql = "DELETE FROM auth_user_xref WHERE auth_type = " . self::PREPARED_STATEMENT_1;
                self::runSQLWithOneClause($sql, $authType, false);

                $sql = "DELETE FROM auth WHERE auth_type = " . self::PREPARED_STATEMENT_1;
                self::runSQLWithOneClause($sql, $authType, false);
            }
        }
    }

    public static function deleteSite($siteName) {

        $sql = "DELETE FROM user_site_xref WHERE site_name = " . self::PREPARED_STATEMENT_1;
        self::runSQLWithOneClause($sql, $siteName, false);

        $sql = "DELETE FROM sites WHERE site_name = " . self::PREPARED_STATEMENT_1;
        self::runSQLWithOneClause($sql, $siteName, false);
    }

    /**
     * Executes SQL query that has no variable clauses
     * @param $sql
     * @return $result
     */
    private static function runSQLWithNoClause($sql, $expectsReturnedData)
    {
        if (HelperFunctions::isLoggedIn() && HelperFunctions::isUserAuthorized(1)) {
            $statement = Database::getDBConnection()->prepare($sql);
            $statement->execute();

            if ($expectsReturnedData) {
                $result = $statement->fetch();
                return $result;
            }

        }
    }

    /**
     * Executes SQL query that has one variable clause
     * @param $sql
     * @param $variableClause
     * @return $result
     */
    private static function runSQLWithOneClause($sql, $variableClause, $expectsReturnedData)
    {
        if (HelperFunctions::isLoggedIn() && HelperFunctions::isUserAuthorized(1)) {
            $statement = Database::getDBConnection()->prepare($sql);
            $statement->bindParam(":prepared_1", $variableClause, PDO::PARAM_STR);
            $statement->execute();

            if ($expectsReturnedData){
                $result = $statement->fetch();
                return $result;
            }
        }
    }

    /**
     * Executes SQL query that has two variable clauses
     * @param $sql
     * @param $firstVariableClause
     * @param $secondVariableClause
     * @return $result
     */
    private static function runSQLWithTwoClauses($sql, $firstVariableClause, $secondVariableClause, $expectsReturnedData)
    {
        if (HelperFunctions::isLoggedIn() && HelperFunctions::isUserAuthorized(1)) {
            $statement = Database::getDBConnection()->prepare($sql);
            $statement->bindParam(":prepared_1", $firstVariableClause, PDO::PARAM_STR);
            $statement->bindParam(":prepared_2", $secondVariableClause, PDO::PARAM_STR);
            $statement->execute();

            if ($expectsReturnedData){
                $result = $statement->fetch();
                return $result;
            }
        }
    }

}

