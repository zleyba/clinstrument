<?php
namespace Edu\Cnm\Zleyba\Clinstrument;

/**
 * User is a class designed to distinguish users who post to our site
 * User contains a user Id # and an email that is associated with that id
 *test
 */

class User {
	/**ID for this user, this is the primary key
	 * @var int $userId
	 **/
private $userId;

	/** email address associated with this account
	 * @var string $email
	 **/
private $email;

	/**
	 * constructor for this user
	 *
	 * @param int|null $newUserId id of this Tweet or null if a new user
	 * @param int $newUserId id of the Profile that sent this user
	 * @throws \InvalidArgumentException if data types are not valid
	 * @throws \RangeException if data values are out of bounds (e.g., strings too long, negative integers)
	 * @throws \TypeError if data types violate type hints
	 * @throws \Exception if some other exception occurs
	 **/
	public function __construct(int $newUserId = null, string $newEmail) {
		try {
			$this->setUserId($newUserId);
			$this->setEmail($newEmail);
		} catch(\InvalidArgumentException $invalidArgument) {
			// rethrow the exception to the caller
			throw(new \InvalidArgumentException($invalidArgument->getMessage(), 0, $invalidArgument));
		} catch(\RangeException $range) {
			// rethrow the exception to the caller
			throw(new \RangeException($range->getMessage(), 0, $range));
		} catch(\TypeError $typeError) {
			// rethrow the exception to the caller
			throw(new \TypeError($typeError->getMessage(), 0, $typeError));
		} catch(\Exception $exception) {
			// rethrow the exception to the caller
			throw(new \Exception($exception->getMessage(), 0, $exception));
		}
	}
	/**
	 * accessor method for user id
	 *
	 * @return int $publicUserId publicly available value of ID#
	 *
	 */

	public function getUserId() {
		return($this->userId);
	}

	/** mutator method for user id
	 *
	 *@param int $newUserId
	 *@throws \UnexpectedValueException if $newUserId is not an integer
	 */

	public function setUserId($newUserId) {
		if($newUserId === null) {
			$this->userId = null;
			return;
		}
		//verify the user id is valid
		$newItemId = filter_var($newUserId, FILTER_VALIDATE_INT);
		if($newUserId === false) {
			throw(new \UnexpectedValueException("user id is not a valid integer"));
		}

		if($newItemId <= 0) {
			throw(new \RangeException("user id is not positive"));
		}

		//convert and store item id
		$this->userId = intval($newUserId);
	}

	/**
	 * accessor method for email
	 *
	 * @return string value of email
	 **/
	public function getEmail() {
		return($this->email);
	}

	/**
	 * mutator method for email
	 *
	 * @param string $newEmail new value of email
	 * @throws \InvalidArgumentException if $newEmail is not a string or insecure
	 * @throws \RangeException if $newEmail is > 128 characters
	 **/
	public function setEmail($newEmail) {
		// verify the email is secure
		$newEmail = trim($newEmail);
		$newEmail = filter_var($newEmail, FILTER_SANITIZE_STRING);
		if(empty($newEmail) === true) {
			throw(new \InvalidArgumentException("Email address empty or insecure"));
		}

		// verify the email address will fit in the database
		if(strlen($newEmail) > 128) {
			throw(new \RangeException("Email address too long"));
		}

		// store the email
		$this->email = $newEmail;
	}

	/**
	 * inserts this User into mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 **/
	public function insert(\PDO $pdo) {
		// enforce the itemId is null (i.e., don't insert a item that already exists)
		if($this->userId !== null) {
			throw(new \PDOException("not a new user"));
		}

		// create query template
		$query	 = "INSERT INTO user(userId, email)
		VALUES(:userId, :email)";
		$statement = $pdo->prepare($query);


		// update the null user with what mySQL just gave us
		$this->userId = intval($pdo->lastInsertId());
	}

	/**
	 * deletes this User from mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 **/
	public function delete(\PDO $pdo) {
		// enforce the itemId is not null (i.e., don't delete an item that hasn't been inserted)
		if($this->userId === null) {
			throw(new \PDOException("unable to delete an item that does not exist"));
		}

		// create query template
		$query	 = "DELETE FROM item WHERE userId = :userId";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holder in the template
		$parameters = array("userId" => $this->userId);
		$statement->execute($parameters);
	}

	/**
	 * updates this User in mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 **/
	public function update(\PDO $pdo) {
		// enforce the itemId is not null (i.e., don't update an item that hasn't been inserted)
		if($this->userId === null) {
			throw(new \PDOException("unable to update an item that does not exist"));
		}

		// create query template
		$query	 = "UPDATE user SET email = :email";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holders in the template
		$parameters = array("email" => $this->email,  "userId" => $this->userId);
		$statement->execute($parameters);
	}

}