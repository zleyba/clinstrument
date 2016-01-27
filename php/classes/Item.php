<?php

// secure PDO connection
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");
$pdo = connectToEncryptedMySQL("/etc/apache2/data-design/zleyba.ini");

/**
 	*Item is an individual listing at a given location
 	*
 	*An item is used to contain all relevant info for an
 	*individual classified listing. This info includes a
 	*unique identification number, the general location,
 	*the title, description of item, and contact info for
 	*the seller. Additionally it may include the price and
 	*photos of the item(s)
 	*
 	*@author Zach Leyba <zleyba@cnm.edu>

 **/

class Item {
	/**ID for this item, this is the primary key
	 * @var int $itemId
	 **/
	private $itemId;

	/**ID# of the user who posted the item, this is the foreign key
	 * @var int $userId
	 **/

	private $userId;

	/** Full length description of the item to be sold
	 * @var  string $itemDescription
	 */

	private $itemDescription;

	/** Reference to any associtated image files
	 * @var string $images
	 */

	private $images;

	/**Contact information for a given seller
	 * @var string $email
	 */

	private $email;


	/**Price in dollars
	 *@var int $price
	 */

	private $price;

	/** Indicates where the seller is and on which city the listing will appear
	 *@var string $location
	 */

	private $location;

	/**
	 * constructor for this Item
	 *
	 * @param int $newItemId id of this Item or null if a new Item
	 * @param int $newUserId id of the User that posted this Item
	 * @param string $newItemDescription string containing actual Item data
	 * @param string $newImages image files accociated with the Item or null if no images
	 * @param string $newEmail contact info for seller of Item
	 * @param int $newPrice cost of Item in dollars
	 * @param string $newLocation location where Item is to be posted
	 * @throws InvalidArgumentException if data types are not valid
	 * @throws RangeException if data values are out of bounds (e.g., strings too long, negative integers)
	 * @throws Exception if some other exception is thrown
	 **/
	public function __construct($newItemId, $newUserId, $newItemDescription, $newImages = null, $newEmail,
										 $newPrice = null, $newLocation) {
		try {
			$this->setItemId($newItemId);
			$this->setUserId($newUserId);
			$this->setItemDescription($newItemDescription);
			$this->setImages($newImages);
			$this->setEmail($newEmail);
			$this->setPrice($newPrice);
			$this->setLocation($newLocation);
		} catch(InvalidArgumentException $invalidArgument) {
			// rethrow the exception to the caller
			throw(new InvalidArgumentException($invalidArgument->getMessage(), 0, $invalidArgument));
		} catch(RangeException $range) {
			// rethrow the exception to the caller
			throw(new RangeException($range->getMessage(), 0, $range));
		} catch(Exception $exception) {
			// rethrow generic exception
			throw(new Exception($exception->getMessage(), 0, $exception));
		}
	}

	/**
	 * accessor method for item id
	 *
	 * @return int $publicItemId publicly available value of ID#
	 *
	 */

	public function getItemId() {
		return($this->itemId);
	}

	/** mutator method for item id
	 *
	 *@param int $newItemId
	 *@throws UnexpectedValueException if $newItemId is not an integer
	 */

	public function setItemId($newItemId) {
		if($newItemId === null) {
			$this->itemId = null;
			return;
		}
		//verify the item id is valid
		$newItemId = filter_var($newItemId, FILTER_VALIDATE_INT);
		if($newItemId === false) {
			throw(new UnexpectedValueException("item id is not a valid integer"));
		}

		if($newItemId <= 0) {
			throw(new RangeException("item id is not positive"));
		}

		//convert and store item id
		$this->itemId = intval($newItemId);
	}

	/**
	 * accessor method for profile id
	 *
	 * @return int value of profile id
	 **/
	public function getUserId() {
		return($this->userId);
	}

	/**
	 * mutator method for user id
	 *
	 * @param int $newUserId new value of user id
	 * @throws InvalidArgumentException if $newUserId is not an integer or not positive
	 * @throws RangeException if $newUserId is not positive
	 **/
	public function setUserId($newUserId) {
		// verify the user id is valid
		$newUserId = filter_var($newUserId, FILTER_VALIDATE_INT);
		if($newUserId === false) {
			throw(new InvalidArgumentException("user id is not a valid integer"));
		}

		// verify the user id is positive
		if($newUserId <= 0) {
			throw(new RangeException("user id is not positive"));
		}

		// convert and store the user id
		$this->userId = intval($newUserId);
	}

	/**
	 * accessor method for item description
	 *
	 * @return string value of item description
	 **/
	public function getItemDescription() {
		return($this->itemDescription);
	}

	/**
	 * mutator method for item description
	 *
	 * @param string $newItemDescription new value of item description
	 * @throws InvalidArgumentException if $newItemDescription is not a string or insecure
	 * @throws RangeException if $newItemDescription is > 2000 characters
	 **/
	public function setItemDescription($newItemDescription) {
		// verify the item description is secure
		$newItemDescription = trim($newItemDescription);
		$newItemDescription = filter_var($newItemDescription, FILTER_SANITIZE_STRING);
		if(empty($newItemDescription) === true) {
			throw(new InvalidArgumentException("Item Description content is empty or insecure"));
		}

		// verify the item decription content will fit in the database
		if(strlen($newItemDescription) > 2000) {
			throw(new RangeException("Item Description too long"));
		}

		// store the tweet content
		$this->itemDescription = $newItemDescription;
	}

	/**
	 * accessor method for images
	 *
	 * @return string value of image file references
	 **/
	public function getImages() {
		return($this->images);
	}

	/**
	 * mutator method for images
	 *
	 * @param string $newImages new value of images
	 * @throws InvalidArgumentException if $newImages is not a string or insecure
	 * @throws RangeException if $newImages is > 64 characters
	 **/
	public function setImages($newImages) {
		// verify the image content is secure
		$newImages = trim($newImages);
		$newImages = filter_var($newImages, FILTER_SANITIZE_STRING);
		if(empty($newImages) === true) {
			throw(new InvalidArgumentException("Image content is insecure"));
		}

		// verify the image file name
		if(strlen($newImages) > 64) {
			throw(new RangeException("Image file length too long"));
		}

		// store the images
		$this->images = $newImages;
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
	 * @param string $newemail new value of email
	 * @throws InvalidArgumentException if $newEmail is not a string or insecure
	 * @throws RangeException if $newEmail is > 128 characters
	 **/
	public function setEmail($newEmail) {
		// verify the email is secure
		$newEmail = trim($newEmail);
		$newEmail = filter_var($newEmail, FILTER_SANITIZE_STRING);
		if(empty($newEmail) === true) {
			throw(new InvalidArgumentException("Email address empty or insecure"));
		}

		// verify the email address will fit in the database
		if(strlen($newEmail) > 128) {
			throw(new RangeException("Email address too long"));
		}

		// store the email
		$this->email = $newEmail;
	}

	/**
	 * accessor method for price
	 *
	 * @return int value of price in dollars
	 **/
	public function getPrice() {
		return($this->price);
	}

	/**
	 * mutator method for price
	 *
	 * @param int $newPrice new value of price
	 * @throws InvalidArgumentException if $newPrice is not an integer
	 * @throws RangeException if $newPrice is > 10 characters
	 **/
	public function setPrice($newPrice) {
		// verify the price is valid
		$newPrice = filter_var($newPrice, FILTER_VALIDATE_INT);
		if($newPrice === false) {
			throw(new InvalidArgumentException("price is not a valid amount"));
		}

		// verify the price is positive
		if($newPrice <= 0) {
			throw(new RangeException("Price is not positive"));
		}

		// convert and store the user id
		$this->price = intval($newPrice);
	}

	/**
	 * accessor method for location
	 *
	 * @return string value of location
	 **/
	public function getLocation() {
		return($this->location);
	}

	/**
	 * mutator method for location
	 *
	 * @param string $newLocation new value of location
	 * @throws InvalidArgumentException if $newLocation is not a string or insecure
	 * @throws RangeException if $newLocation is > 64 characters
	 **/
	public function setLocation($newLocation) {
		// verify the location is secure
		$newLocation = trim($newLocation);
		$newLocation = filter_var($newLocation, FILTER_SANITIZE_STRING);
		if(empty($newLocation) === true) {
			throw(new InvalidArgumentException("location is empty or insecure"));
		}

		// verify the location name will fit in the database
		if(strlen($newLocation) > 64) {
			throw(new RangeException("location name too long"));
		}

		// store the location
		$this->location = $newLocation;
	}

	/**
	 * inserts this Item into mySQL
	 *
	 * @param PDO $pdo PDO connection object
	 * @throws PDOException when mySQL related errors occur
	 **/
	public function insert(PDO $pdo) {
		// enforce the itemId is null (i.e., don't insert a item that already exists)
		if($this->itemId !== null) {
			throw(new PDOException("not a new item"));
		}

		// create query template
		$query	 = "INSERT INTO item(itemId, userId, itemDescription, images, email, price, location)
		VALUES(:itemId, :itemDescription, :images, :email, :price, :location)";
		$statement = $pdo->prepare($query);


		// update the null tweetId with what mySQL just gave us
		$this->itemId = intval($pdo->lastInsertId());
	}

	/**
	 * deletes this Item from mySQL
	 *
	 * @param PDO $pdo PDO connection object
	 * @throws PDOException when mySQL related errors occur
	 **/
	public function delete(PDO $pdo) {
		// enforce the itemId is not null (i.e., don't delete an item that hasn't been inserted)
		if($this->itemId === null) {
			throw(new PDOException("unable to delete an item that does not exist"));
		}

		// create query template
		$query	 = "DELETE FROM item WHERE itemId = :itemId";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holder in the template
		$parameters = array("itemId" => $this->itemId);
		$statement->execute($parameters);
	}

	/**
	 * updates this item in mySQL
	 *
	 * @param PDO $pdo PDO connection object
	 * @throws PDOException when mySQL related errors occur
	 **/
	public function update(PDO $pdo) {
		// enforce the itemId is not null (i.e., don't update an item that hasn't been inserted)
		if($this->itemId === null) {
			throw(new PDOException("unable to update an item that does not exist"));
		}

		// create query template
		$query	 = "UPDATE item SET userId = :userId, itemDescription = :itemDescription, images = :images, email = :email, price = :price, location = :location
 		WHERE itemId = :itemId";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holders in the template
		$parameters = array("userId" => $this->userId, "itemDescription" => $this->itemDescription, "images" => $this->images,
			"email" => $this->email, "price" => $this->price, "location" => $this->location, "itemId" => $this->itemId);
		$statement->execute($parameters);
	}

}


