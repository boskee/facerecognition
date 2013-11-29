<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);

require_once 'vendor/autoload.php';

class Image
{
	public $status;
	public $file;
	public $width;
	public $height;
	public $image_id;
	public $image_expiration;
	public $faces;
	public $imageUrl;
	public $features = "FULL";

	public function __construct($imageUrl, $features = "FULL")
	{
		$this->imageUrl = $imageUrl;
		$this->features = $features;
		$this->detect();
	}

	public function enroll($subject_id, $gallery_id)
	{
		$manager = FaceManager::getInstance();

		$params = array(
			'subject_id' => $subject_id,
			'gallery_id' => $gallery_id,
			'image_id' => $this->image_id,
			/*'topLeftX' => $this->faces[0]->topLeftX,
			'topLeftY' => $this->faces[0]->topLeftY,
			'width' => $this->faces[0]->width,
			'height' => $this->faces[0]->height,*/
			'leftEyeCenterX' => $this->faces[0]->leftEyeCenterX,
			'leftEyeCenterY' => $this->faces[0]->leftEyeCenterY,
			'rightEyeCenterX' => $this->faces[0]->rightEyeCenterX,
			'rightEyeCenterY' => $this->faces[0]->rightEyeCenterY
		);

		$strings = array();

		foreach ($params AS $key => $value)
		{
			$strings[] = $key . '=' . $value;
		}

		$response = Unirest::get(
		  $manager->getBaseUrl("enroll") . "&" . implode('&', $strings),
		  array(
		    "X-Mashape-Authorization" => $manager->getMashapeKey()
		  ),
		  null
		);

		// object(Unirest\HttpResponse)#8 (4) { ["code":"Unirest\HttpResponse":private]=> int(200) ["raw_body":"Unirest\HttpResponse":private]=> string(203) "{"images":[{"time":4.08357,"transaction":{"status":"Complete","face_id":"96626c4628c2d13e7327d6853b9fa6aa","image_id":"ec77408d9447d6833b1247fc6b94320a","subject_id":"Anita_Toro","gallery_id":"Stars"}}]}" ["body":"Unirest\HttpResponse":private]=> object(stdClass)#9 (1) { ["images"]=> array(1) { [0]=> object(stdClass)#10 (2) { ["time"]=> float(4.08357) ["transaction"]=> object(stdClass)#11 (5) { ["status"]=> string(8) "Complete" ["face_id"]=> string(32) "96626c4628c2d13e7327d6853b9fa6aa" ["image_id"]=> string(32) "ec77408d9447d6833b1247fc6b94320a" ["subject_id"]=> string(10) "Anita_Toro" ["gallery_id"]=> string(5) "Stars" } } } } ["headers":"Unirest\HttpResponse":private]=> array(8) { ["content-type"]=> string(16) "application/json" ["date"]=> string(29) "Thu, 28 Nov 2013 20:48:39 GMT" ["server"]=> string(6) "Apache" ["X-Mashape-Proxy-Response"]=> string(5) "false" ["X-Mashape-Version"]=> string(5) "3.1.4" ["x-powered-by"]=> string(10) "PHP/5.3.18" ["Content-Length"]=> string(3) "203" ["Connection"]=> string(10) "keep-alive" } }
	}

	public function recognize($gallery_id)
	{
		$manager = FaceManager::getInstance();

		$params = array(
			'gallery_id' => $gallery_id,
			'image_id' => $this->image_id,
			/*'topLeftX' => $this->faces[0]->topLeftX,
			'topLeftY' => $this->faces[0]->topLeftY,
			'width' => $this->faces[0]->width,
			'height' => $this->faces[0]->height,*/
			'leftEyeCenterX' => $this->faces[0]->leftEyeCenterX,
			'leftEyeCenterY' => $this->faces[0]->leftEyeCenterY,
			'rightEyeCenterX' => $this->faces[0]->rightEyeCenterX,
			'rightEyeCenterY' => $this->faces[0]->rightEyeCenterY
		);

		$strings = array();

		foreach ($params AS $key => $value)
		{
			$strings[] = $key . '=' . $value;
		}

		$response = Unirest::get(
		  $manager->getBaseUrl("recognize") . "&" . implode('&', $strings),
		  array(
		    "X-Mashape-Authorization" => $manager->getMashapeKey()
		  ),
		  null
		);

		var_dump($response);
	}

	public function detect()
	{
		$manager = FaceManager::getInstance();

		$response = Unirest::post(
		  $manager->getBaseUrl("detect"),
		  array(
		    "X-Mashape-Authorization" => $manager->getMashapeKey()
		  ),
		  array(
		    "selector" => $this->features,
		    "url" => addslashes($this->imageUrl)
		  )
		);

		foreach ($response->body->images AS $image)
		{
	        foreach (get_object_vars($image) as $key => $value)
	        {
	            $this->$key = $value;
	        }
	    }

		return $response->body;
	}
}

class FaceManager
{
	private $apiKey = null;
	private $mashapeKey = null;
	static private $instance = null;

	private function __construct($apiKey, $mashapeKey)
	{
		$this->apiKey = $apiKey;
		$this->mashapeKey = $mashapeKey;
	}

	static public function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = new FaceManager('dbc7de8bbf560a61881afb0af37868a7', 'eSlyIMkzCh4vq7wTZED4LDHLGg7PfmZX');
		}

		return self::$instance;
	}

	public function getBaseUrl($methodName)
	{
		return "https://animetrics.p.mashape.com/" . $methodName . "?api_key=" . $this->apiKey;
	}

	public function getMashapeKey()
	{
		return $this->mashapeKey;
	}
}

/*$face = new Image("http://i0.bzpics.com/models/2091/model-large.jpg");
$face->recognize("Stars");*/

/*$face = new Image("http://i0.bzpics.com/models/2067/model-large.jpg");
$face->enroll("Alex_Chance", "Stars");
//$face->recognize("Stars");*/

$face = new Image("http://www.exposay.com/celebrity-photos/alex-chance-heaven-hell-2013-halloween-bash-cbAiR4.jpg");
//$face->enroll("Alex_Chance", "Stars");
$face->recognize("Stars");

echo 'a';
/*
*/

/*$response = Unirest::get(
  "https://animetrics.p.mashape.com/enroll?api_key=" . $apiKey . "&subject_id=Megan_Salinas&gallery_id=Stars&image_id=b4bc182b406cb9f67913adbf0f0ebf37&topLeftX=140&topLeftY=67&width=386&height=520",
  array(
    "X-Mashape-Authorization" => $mashapeKey
  ),
  null
);*/

/*
$response = Unirest::get(
  "https://animetrics.p.mashape.com/add_to_gallery?api_key=" . $apiKey . "&subject_id=Megan_Salinas&gallery_id=Stars",
  array(
    "X-Mashape-Authorization" => $mashapeKey
  ),
  null
);
*/

/*
$response = Unirest::get(
  "https://animetrics.p.mashape.com/view_gallery?api_key=" . $apiKey . "&gallery_id=Stars",
  array(
    "X-Mashape-Authorization" => $mashapeKey
  ),
  null
);

var_dump($response);
*/

// object(Unirest\HttpResponse)#2 (4) { ["code":"Unirest\HttpResponse":private]=> int(200) ["raw_body":"Unirest\HttpResponse":private]=> string(1385) "{"images":[{"time":1.40372,"status":"Complete","file":"http:\/\/i0.bzpics.com\/models\/2135\/model-large.jpg","width":386,"height":520,"image_id":"b4bc182b406cb9f67913adbf0f0ebf37","image_expiration":"2013-11-28 19:36 -0500","faces":[{"topLeftX":140,"topLeftY":67,"width":73,"height":73,"leftEyeCenterX":-1,"leftEyeCenterY":-1,"rightEyeCenterX":-1,"rightEyeCenterY":-1,"noseTipX":-1,"noseTipY":-1,"noseBtwEyesX":-1,"noseBtwEyesY":-1,"chinTipX":-1,"chinTipY":-1,"leftEyeCornerLeftX":-1,"leftEyeCornerLeftY":-1,"leftEyeCornerRightX":-1,"leftEyeCornerRightY":-1,"rightEyeCornerLeftX":-1,"rightEyeCornerLeftY":-1,"rightEyeCornerRightX":-1,"rightEyeCornerRightY":-1,"rightEarTragusX":-1,"rightEarTragusY":-1,"leftEarTragusX":-1,"leftEarTragusY":-1,"leftEyeBrowLeftX":-1,"leftEyeBrowLeftY":-1,"leftEyeBrowMiddleX":-1,"leftEyeBrowMiddleY":-1,"leftEyeBrowRightX":-1,"leftEyeBrowRightY":-1,"rightEyeBrowLeftX":-1,"rightEyeBrowLeftY":-1,"rightEyeBrowMiddleX":-1,"rightEyeBrowMiddleY":-1,"rightEyeBrowRightX":-1,"rightEyeBrowRightY":-1,"nostrilLeftHoleBottomX":-1,"nostrilLeftHoleBottomY":-1,"nostrilRightHoleBottomX":-1,"nostrilRightHoleBottomY":-1,"nostrilLeftSideX":-1,"nostrilLeftSideY":-1,"nostrilRightSideX":-1,"nostrilRightSideY":-1,"lipCornerLeftX":-1,"lipCornerLeftY":-1,"lipLineMiddleX":-1,"lipLineMiddleY":-1,"lipCornerRightX":-1,"lipCornerRightY":-1,"pitch":-1,"yaw":-1,"roll":-1}]}]}" ["body":"Unirest\HttpResponse":private]=> object(stdClass)#3 (1) { ["images"]=> array(1) { [0]=> object(stdClass)#4 (8) { ["time"]=> float(1.40372) ["status"]=> string(8) "Complete" ["file"]=> string(48) "http://i0.bzpics.com/models/2135/model-large.jpg" ["width"]=> int(386) ["height"]=> int(520) ["image_id"]=> string(32) "b4bc182b406cb9f67913adbf0f0ebf37" ["image_expiration"]=> string(22) "2013-11-28 19:36 -0500" ["faces"]=> array(1) { [0]=> object(stdClass)#5 (55) { ["topLeftX"]=> int(140) ["topLeftY"]=> int(67) ["width"]=> int(73) ["height"]=> int(73) ["leftEyeCenterX"]=> int(-1) ["leftEyeCenterY"]=> int(-1) ["rightEyeCenterX"]=> int(-1) ["rightEyeCenterY"]=> int(-1) ["noseTipX"]=> int(-1) ["noseTipY"]=> int(-1) ["noseBtwEyesX"]=> int(-1) ["noseBtwEyesY"]=> int(-1) ["chinTipX"]=> int(-1) ["chinTipY"]=> int(-1) ["leftEyeCornerLeftX"]=> int(-1) ["leftEyeCornerLeftY"]=> int(-1) ["leftEyeCornerRightX"]=> int(-1) ["leftEyeCornerRightY"]=> int(-1) ["rightEyeCornerLeftX"]=> int(-1) ["rightEyeCornerLeftY"]=> int(-1) ["rightEyeCornerRightX"]=> int(-1) ["rightEyeCornerRightY"]=> int(-1) ["rightEarTragusX"]=> int(-1) ["rightEarTragusY"]=> int(-1) ["leftEarTragusX"]=> int(-1) ["leftEarTragusY"]=> int(-1) ["leftEyeBrowLeftX"]=> int(-1) ["leftEyeBrowLeftY"]=> int(-1) ["leftEyeBrowMiddleX"]=> int(-1) ["leftEyeBrowMiddleY"]=> int(-1) ["leftEyeBrowRightX"]=> int(-1) ["leftEyeBrowRightY"]=> int(-1) ["rightEyeBrowLeftX"]=> int(-1) ["rightEyeBrowLeftY"]=> int(-1) ["rightEyeBrowMiddleX"]=> int(-1) ["rightEyeBrowMiddleY"]=> int(-1) ["rightEyeBrowRightX"]=> int(-1) ["rightEyeBrowRightY"]=> int(-1) ["nostrilLeftHoleBottomX"]=> int(-1) ["nostrilLeftHoleBottomY"]=> int(-1) ["nostrilRightHoleBottomX"]=> int(-1) ["nostrilRightHoleBottomY"]=> int(-1) ["nostrilLeftSideX"]=> int(-1) ["nostrilLeftSideY"]=> int(-1) ["nostrilRightSideX"]=> int(-1) ["nostrilRightSideY"]=> int(-1) ["lipCornerLeftX"]=> int(-1) ["lipCornerLeftY"]=> int(-1) ["lipLineMiddleX"]=> int(-1) ["lipLineMiddleY"]=> int(-1) ["lipCornerRightX"]=> int(-1) ["lipCornerRightY"]=> int(-1) ["pitch"]=> int(-1) ["yaw"]=> int(-1) ["roll"]=> int(-1) } } } } } ["headers":"Unirest\HttpResponse":private]=> array(8) { ["content-type"]=> string(16) "application/json" ["date"]=> string(29) "Thu, 28 Nov 2013 19:36:13 GMT" ["server"]=> string(6) "Apache" ["X-Mashape-Proxy-Response"]=> string(5) "false" ["X-Mashape-Version"]=> string(5) "3.1.4" ["x-powered-by"]=> string(10) "PHP/5.3.18" ["Content-Length"]=> string(4) "1385" ["Connection"]=> string(10) "keep-alive" } }


// object(Unirest\HttpResponse)#2 (4) { ["code":"Unirest\HttpResponse":private]=> int(200) ["raw_body":"Unirest\HttpResponse":private]=> string(153) "{"errors":{"dimensions":"must specify either (topLeftX, topLeftY, width, height) or (leftEyeCenterX, leftEyeCenterY, rightEyeCenterX, rightEyeCenterY)"}}" ["body":"Unirest\HttpResponse":private]=> object(stdClass)#3 (1) { ["errors"]=> object(stdClass)#4 (1) { ["dimensions"]=> string(125) "must specify either (topLeftX, topLeftY, width, height) or (leftEyeCenterX, leftEyeCenterY, rightEyeCenterX, rightEyeCenterY)" } } ["headers":"Unirest\HttpResponse":private]=> array(8) { ["content-type"]=> string(16) "application/json" ["date"]=> string(29) "Thu, 28 Nov 2013 19:42:50 GMT" ["server"]=> string(6) "Apache" ["X-Mashape-Proxy-Response"]=> string(5) "false" ["X-Mashape-Version"]=> string(5) "3.1.4" ["x-powered-by"]=> string(10) "PHP/5.3.18" ["Content-Length"]=> string(3) "153" ["Connection"]=> string(10) "keep-alive" } }

// object(Unirest\HttpResponse)#2 (4) { ["code":"Unirest\HttpResponse":private]=> int(200) ["raw_body":"Unirest\HttpResponse":private]=> string(206) "{"images":[{"time":4.12158,"transaction":{"status":"Complete","face_id":"135350de9c13633851171b26000a22d0","image_id":"b4bc182b406cb9f67913adbf0f0ebf37","subject_id":"Megan_Salinas","gallery_id":"Stars"}}]}" ["body":"Unirest\HttpResponse":private]=> object(stdClass)#3 (1) { ["images"]=> array(1) { [0]=> object(stdClass)#4 (2) { ["time"]=> float(4.12158) ["transaction"]=> object(stdClass)#5 (5) { ["status"]=> string(8) "Complete" ["face_id"]=> string(32) "135350de9c13633851171b26000a22d0" ["image_id"]=> string(32) "b4bc182b406cb9f67913adbf0f0ebf37" ["subject_id"]=> string(13) "Megan_Salinas" ["gallery_id"]=> string(5) "Stars" } } } } ["headers":"Unirest\HttpResponse":private]=> array(8) { ["content-type"]=> string(16) "application/json" ["date"]=> string(29) "Thu, 28 Nov 2013 19:44:31 GMT" ["server"]=> string(6) "Apache" ["X-Mashape-Proxy-Response"]=> string(5) "false" ["X-Mashape-Version"]=> string(5) "3.1.4" ["x-powered-by"]=> string(10) "PHP/5.3.18" ["Content-Length"]=> string(3) "206" ["Connection"]=> string(10) "keep-alive" } }