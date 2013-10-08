<?php
class graphicMail
{
    private $username;
    private $password;
    private $APIURL;
    private $userData;

    public function __construct($username, $password) 
    {
        $this->username = (string) $username;
        $this->password = (string) $password;
        $this->APIURL = "https://www.graphicmail.com/api.aspx?Username=$this->username&Password=$this->password";
    	$this->userData = array();
    }

    public function sendRequestGraphicMail($mailQuery)
    {
		$curl_connection = curl_init($mailQuery);
		curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
		$result = curl_exec($curl_connection);
		curl_close($curl_connection);
		return $result;
       
    }

    /*
		Function will return the ID of the newly created Mail List or 0 if the creation failed
    */
    public function newMailList($myMailName) 
    {
    	$graphicMailGet = "{$this->APIURL}&Function=post_create_mailinglist&NewMailinglist={$myMailName}&ReturnMailingListID=true&SID=6";
    	$graphicMailReturned = $this->sendRequestGraphicMail($graphicMailGet);
    	$graphicMailExploded = explode('|', $graphicMailReturned);

    	// Check to see if successful
    	if ($graphicMailExploded[0] == 0) 
    	{
    		return 0;
    	}
    	else
    	{
    		return $graphicMailExploded[1];
    	}
    }

    /*
		Function will return all Mail Lists in an array with the Mail List Name as the key
    */
    public function getMailLists() 
    {
    	$graphicMailGet = "{$this->APIURL}&Function=get_mailinglists&SID=0";
    	$graphicMailReturned = $this->sendRequestGraphicMail($graphicMailGet);
    	if (!$graphicMailReturned){
			return false;
		}
		$myLists = simplexml_load_string($graphicMailReturned);
		$listArray = array();
		foreach($myLists->mailinglist as $newsletter){
			$listArray[(string)$newsletter->description]= (int)$newsletter->mailinglistid;
		}
		return $listArray; 
		
    }

    /*
		Function will subscribe the email address to the passed Mail List
    */
    public function subMailList($emailAddress, $mailList) 
    {
    	$graphicMailGet = "{$this->APIURL}&Function=post_subscribe&Email={$emailAddress}&MailinglistID={$mailList}&SID=6";
    	$graphicMailReturned = $this->sendRequestGraphicMail($graphicMailGet);
    	$graphicMailExploded = explode('|', $graphicMailReturned);

    	// Check to see if successful
    	if ($graphicMailExploded[0] == 0) 
    	{
    		// Error
    		return 0;
    	}
    	elseif($graphicMailExploded[0] == 1)
    	{
    		// Added Sucessfully
    		return 1;
    	}
    	else
    	{
    		// Email Already Subscribed
    		return 2;
    	}
    }

    /*
		Get a list of all datasets
    */
    public function getDataSets() 
    {
    	$graphicMailGet = "{$this->APIURL}&Function=get_datasets&SID=0";
		
    	$graphicMailReturned = $this->sendRequestGraphicMail($graphicMailGet);
		if (!$graphicMailReturned){
			return false;
		}
    	$myLists = simplexml_load_string($graphicMailReturned);
    	$listArray = array();
		foreach ($myLists as $list){
			$listArray[(string) $list->name] = (int) $list->datasetid;
		}
		
		return $listArray; 
 
	    
    }


    /*
		Set a users data ready to update
    */
    public function setUserData($type, $value)
    {
    	switch ($type) 
    	{
    		case 'mobile':
    			$this->userData['mobile'] = $value;
    			break;
    		case 'fname':
    			$this->userData['fname'] = $value;
    			break;
    		case 'sname':
    			$this->userData['sname'] = $value;
    			break;
    		case 'title':
    			$this->userData['title'] = $value;
    			break;
    		case 'company':
    			$this->userData['company'] = $value;
    			break;
    		case 'jobtitle':
    			$this->userData['jTitle'] = $value;
    			break;
    		case 'worktel':
    			$this->userData['telWork'] = $value;
    			break;
    		case 'workfax':
    			$this->userData['faxWork'] = $value;
    			break;
    		case 'hometel':
    			$this->userData['telHome'] = $value;
    			break;
    		case 'addr1':
    			$this->userData['addressOne'] = $value;
    			break;
    		case 'addr2':
    			$this->userData['addressTwo'] = $value;
    			break;
    		case 'city':
    			$this->userData['city'] = $value;
    			break;
    		case 'county':
    			$this->userData['county'] = $value;
    			break;
    		case 'postcode':
    			$this->userData['postcode'] = $value;
    			break;
    		case 'country':
    			$this->userData['country'] = $value;
    			break;
    		case 'dob':
    			$this->userData['birthday'] = $value;
    			break;
    		case 'gender':
    			$this->userData['gender'] = $value;
    			break;
    		case 'website':
    			$this->userData['website'] = $value;
    			break;
    		case 'imtype':
    			$this->userData['imtype'] = $value;
    			break;
    		case 'imaddress':
    			$this->userData['imaddress'] = $value;
    			break;
    		case 'notes':
    			$this->userData['notes'] = $value;
    			break;
    		default:
    			break;
    	}
    }

    /*
		Update dataset with users data
    */
    public function insertToDataSet($emailAddress,$dataSetId) 
    {
    	$graphicMailGet = "{$this->APIURL}&Function=post_insertdata&Email={$emailAddress}&DatasetID={$dataSetId}";
    	if(isset($this->userData['mobile']))
    		$graphicMailGet .= "&MobileNumber={$this->userData['mobile']}";    	
    	if(isset($this->userData['fname']))
    		$graphicMailGet .= "&Col1={$this->userData['fname']}";    	
    	if(isset($this->userData['sname']))
    		$graphicMailGet .= "&Col2={$this->userData['sname']}";    	
    	if(isset($this->userData['title']))
    		$graphicMailGet .= "&Col3={$this->userData['title']}";    	
    	if(isset($this->userData['company']))
    		$graphicMailGet .= "&Col4={$this->userData['company']}";
    	if(isset($this->userData['jTitle']))
    		$graphicMailGet .= "&Col5={$this->userData['jTitle']}";
    	if(isset($this->userData['telWork']))
    		$graphicMailGet .= "&Col6={$this->userData['telWork']}";
    	if(isset($this->userData['faxWork']))
    		$graphicMailGet .= "&Col7={$this->userData['faxWork']}";
    	if(isset($this->userData['telHome']))
    		$graphicMailGet .= "&Col8={$this->userData['telHome']}";
    	if(isset($this->userData['addressOne']))
    		$graphicMailGet .= "&Col9={$this->userData['addressOne']}";
    	if(isset($this->userData['addressTwo']))
    		$graphicMailGet .= "&Col10={$this->userData['addressTwo']}";
    	if(isset($this->userData['city']))
    		$graphicMailGet .= "&Col11={$this->userData['city']}";
    	if(isset($this->userData['county']))
    		$graphicMailGet .= "&Col12={$this->userData['county']}";
    	if(isset($this->userData['postcode']))
    		$graphicMailGet .= "&Col13={$this->userData['postcode']}";
    	if(isset($this->userData['country']))
    		$graphicMailGet .= "&Col14={$this->userData['country']}";
    	if(isset($this->userData['birthday']))
    		$graphicMailGet .= "&Col15={$this->userData['birthday']}";
    	if(isset($this->userData['gender']))
    		$graphicMailGet .= "&Col16={$this->userData['gender']}";
    	if(isset($this->userData['website']))
    		$graphicMailGet .= "&Col17={$this->userData['website']}";
    	if(isset($this->userData['imtype']))
    		$graphicMailGet .= "&Col18={$this->userData['imtype']}";
    	if(isset($this->userData['imaddress']))
    		$graphicMailGet .= "&Col19={$this->userData['imaddress']}";
    	if(isset($this->userData['notes']))
    		$graphicMailGet .= "&Col20={$this->userData['notes']}";
    	$graphicMailGet = str_replace(' ', '%20', $graphicMailGet);

    	$graphicMailReturned = $this->sendRequestGraphicMail($graphicMailGet);
    	$graphicMailExploded = explode('|', $graphicMailReturned);

    	// Check to see if successful
    	if ($graphicMailExploded[0] == 0) 
    	{
    		// Error
    		return 0;
    	}
    	elseif($graphicMailExploded[0] == 1)
    	{
    		// Inserted Sucessfully
    		return 1;
    	}
    	else
    	{
    		// Updated Sucessfully
    		return 2;
    	}
    	unset($this->userData);
    }
	
	/*
		Deletes the specified email address from the specified mailing list. (post_delete_emailaddress)
		* @email - string email address (optional)
		* @listID - integer, id of the maling list (required)
		* @emailID - integer, id of the email address you want to delete (optional)
		If email or emailID are not specified deletes all the emails in the mailing list.
		
	*/
	public function deleteEmailAddress($listID = null, $email = null, $emailID = null){
	
		if(!$listID) {
			return false;
		}
		$additionalParameters = '&Function=post_delete_emailaddress';
		if ($email){
			$additionalParameters .= "&EmailAddress=$email";
		}
		if ($emailID){
			$additionalParameters .= "&EmailID=$emailID";
		}
		$additionalParameters .= "&MailinglistID=$listID&SID=0";
		$graphicMailGet = "{$this->APIURL}$additionalParameters";
		$graphicMailReturned = $this->sendRequestGraphicMail($graphicMailGet);
		if (!$graphicMailReturned){
			return false;
		}
		$graphicMailExploded = explode('|', $graphicMailReturned);
		if ($graphicMailExploded[0] == 0) 
    	{
    		// Error
    		return 0;
    	}
    	elseif($graphicMailExploded[0] == 1)
    	{
    		// Added Sucessfully
    		return 1;
    	}
	}
	/*
		Deletes all email addresses from the specified dataset (post_delete_from_dataset)
		* @email - string email address (optional)
		* @datasetID - integer, id of the dataset (required)
		* @emailID - integer, id of the email address you want to delete (optional)
		If email or emailID are not specified deletes all the emails in the mailing list.
	*/
	public function deleteFromDataset($datasetID = null, $email = null, $emailID = null){
		if(!$datasetID) {
			return false;
		}
		$additionalParameters = '&Function=post_delete_from_dataset';
		if ($email){
			$additionalParameters .= "&EmailAddress=$email";
		}
		if ($emailID){
			$additionalParameters .= "&EmailID=$emailID";
		}
		$additionalParameters .= "&DatasetID=$datasetID&SID=0";
		$graphicMailGet = "{$this->APIURL}$additionalParameters";
		$graphicMailReturned = $this->sendRequestGraphicMail($graphicMailGet);
		if (!$graphicMailReturned){
			return false;
		}
		$graphicMailExploded = explode('|', $graphicMailReturned);
		if ($graphicMailExploded[0] == 0) 
    	{
    		// Error
    		return 0;
    	}
    	elseif($graphicMailExploded[0] == 1)
    	{
    		// Added Sucessfully
    		return 1;
    	}
	}
	/*
		 Sends a specified newsletter to a specified mailing list ( post_sendmail )
	*/
	public function sendMail($embedImages = "false", $fromEmail = null, $fromName = null, $mailingListID = null, $newsletterID = null, $returnSendID = "false", $subject = null, $textOnly = 0){
		if(!$fromEmail or !$fromName or !$mailingListID or !$newsletterID or !$subject ) {
			return false;
		}
		$additionalParameters = '&Function=post_sendmail&EmbedImages='.$embedImages.'&FromEmail='.$fromEmail. '&FromName='.urlencode($fromName).'&MailinglistID='.$mailingListID.'&NewsletterID='.$newsletterID.'&ReturnSendID='.$returnSendID.'&Subject='.urlencode($subject).'&TextOnly='.$textOnly.'&SID=0';
		$graphicMailGet = $this->APIURL.$additionalParameters;
		
		$graphicMailReturned = $this->sendRequestGraphicMail($graphicMailGet);
		$graphicMailExploded = explode('|', $graphicMailReturned);
		var_dump($graphicMailExploded);
		if ($graphicMailExploded[0] == 0){
			return false;
		}
		
		return $graphicMailExploded;
	}
	/*
		 get_newsletters 
	*/
	
	public function getNewsletters (){
		$additionalParameters = '&Function=get_newsletters&SID=0';
		$graphicMailGet = $this->APIURL.$additionalParameters;
		$graphicMailReturned = $this->sendRequestGraphicMail($graphicMailGet);
		if (!$graphicMailReturned){
			return false;
		}
		$myLists = simplexml_load_string($graphicMailReturned);
		$listArray = array();
		foreach($myLists->newsletter as $newsletter){
			$listArray[(string)$newsletter->newslettername]= (int)$newsletter->newsletterid;
		}
		return $listArray; 
		
	}
	
	/*
		Returns all the email addresses in the specified mailing list 
		@mailingList - 
	*/
	public function getMailinglist($mailingList = null)
	{
		if (!$mailingList){
			return false;
		}
		$additionalParameters = '&Function=get_mailinglist';
		$additionalParameters .= '&MailinglistID='.$mailingList."&SID=0";
		$graphicMailGet = $this->APIURL.$additionalParameters;
		$graphicMailReturned = $this->sendRequestGraphicMail($graphicMailGet);
		$graphicMailExploded = explode('|', $graphicMailReturned);
		 
		//var_dump( $graphicMailExploded[0]);
		if (empty($graphicMailReturned)){
			//var_dump($graphicMailExploded[0]);
			return false;
		}
		//if ()
		$myLists = simplexml_load_string($graphicMailReturned);
		$listArray = array();
		foreach($myLists->email as $email){
			$listArray[(string)$email->emailaddress]= (int)$email->emailid;
		}
		return $listArray; 
	}
	
	/*
		*post_import_dataset 
	
	*/
	public function importDataset($datasetID = null, $emailCol = null, $fileURL = null, $mailingListID = null, $col = array(), $importMode = 1, $mobileCol = null, $mobileListID = null, $sheetName = null, $isCSV = 'true' ){
		if (!$datasetID or !$fileURL){
			return 1;
		}
		if (!$emailCol and !$mobileCol){
			return 2;
		}
		if (!$mailingListID and !$mobileListID){
			return 3;
		}
		if (($emailCol and !$mailingListID ) or ($mobileCol and !$mobileListID)){
			return 4;
		}
		if (count($col)<1){
			return 5;
		}
		
		$additionalParameters = '&Function=post_import_dataset';
		$additionalParameters .= '&DatasetID='.$datasetID;
		$additionalParameters .= '&FileUrl='.$fileURL;
		foreach ($col as $key=>$value){
			$additionalParameters .= '&'.$key.'='.$value;
		}
		if ($emailCol){
			$additionalParameters .= "&EmailCol=$emailCol";
		}
		if ($mobileCol){
			$additionalParameters .= "&MobileCol=$mobileCol";
		}
		if ($mailingListID){
			$additionalParameters .= "&MailingListID=$mailingListID";
		}
		if ($mobileListID){
			$additionalParameters .= "&MobileListID=$mobileListID";
		}
		if ($sheetName){
			$additionalParameters .= "&Sheetname=$sheetName";
		}
		
		
		$additionalParameters .= "&ImportMode=$importMode&IsCsv=$isCSV&SID=0";
		$graphicMailGet = $this->APIURL.$additionalParameters;
		$graphicMailReturned = $this->sendRequestGraphicMail($graphicMailGet);
		$graphicMailExploded = explode('|', $graphicMailReturned);
		//var_dump($graphicMailReturned);
		if (!$graphicMailReturned or $graphicMailExploded[0] == 0){
			return 6;
		}
		return $graphicMailExploded;
		
	}
}
?>
