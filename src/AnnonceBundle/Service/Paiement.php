<?php

namespace AnnonceBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;

use AdminBundle\Entity\userAdmin;

use MangoPay\UserNatural;
use MangoPay\UserLegal;
use MangoPay\MangoPayApi;
use MangoPay\Address;
use MangoPay\CardRegistration;
use MangoPay\Wallet;

use MangoPay\PayIn;
use MangoPay\PayInStatus;

use MangoPay\PayOut;
use MangoPay\PayOutStatus;

use MangoPay\Money;
use MangoPay\PayInPaymentDetailsCard;
use MangoPay\PayInExecutionDetailsDirect;

use MangoPay\PayOutPaymentDetails;
use MangoPay\PayOutPaymentDetailsBankWire;


use MangoPay\BankAccount;
use MangoPay\BankAccountDetails;
use MangoPay\BankAccountDetailsIBAN;
use MangoPay\BankAccountDetailsOTHER;


use MangoPay\Libraries\Logs;

class Paiement{

	private $requestObj;
	private $session;
	private $em;
	private $paiement;
	private $user;
	private $api;

	private $clientId;
	private $clefApi;
	private $password;
	private $passWebmaster;
	private $folder;
	private $currency;
	private $url;
	public $partEntreprise;
	private $partCommercial;
	private $partWebmaster;

	public function __construct(EntityManager $entityManager, Session $session, $arrayParams){

		$this->requestObj = null;
		$this->session = $session;
		$this->em = $entityManager;

		$this->clientId = $arrayParams['clientId'];
		$this->clefApi = $arrayParams['clefApi'];
		$this->password = $arrayParams['password'];
		$this->passWebmaster = $arrayParams['passWebmaster'];
		$this->folder = $arrayParams['folder'];
		$this->currency = $arrayParams['currency'];
		$this->url = $arrayParams['url'];
		$this->partEntreprise = $arrayParams['parts']['entreprise'];
		$this->partWebmaster = $arrayParams['parts']['webmaster'];
		$this->partCommercial = $arrayParams['parts']['commercial'];

		$this->userCommercial = $arrayParams['users']['commercial'];
		$this->userDevelopper = $arrayParams['users']['developper'];
	}


	public function setRequest(Request $request){
		$this->requestObj = $request;
		return $this;
	}


	public function setUser($user){
		$this->user = $user;
		return $this;
	}

	public function setPaiement($user){
		$this->paiement = $user;
		return $this;
	}

	public function setUrl($url){
		$this->url = $url;
		return $this;
	}

	public function getParts(){
		return array(
			"entreprise"=>$this->partEntreprise,
			"webmaster"=>$this->partWebmaster,
			"commercial"=>$this->partCommercial
		);
	}


	public function genCardPaie($user){
		try{
			return $this->createCard($user);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}


	public function genPaiementClient(){
		try{
			return $this->createPaiementClient();
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}


	public function genPaiementFournisseur(){
		try{
			return $this->createPaiementFournisseur();
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}


	public function getPartEntreprise(){
		try{
			$reservation = $this->session->get("reservation");
			$partEntreprise = ($reservation->getPrix()*100 * $this->partEntreprise)/100;
			return $partEntreprise;
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}





	private function createCard($user){
		try{

			$session = $this->session;


			$api = new MangoPayApi();
	        $api->Config->ClientId = $this->clientId;
	        $api->Config->ClientPassword = $this->clefApi;
	        $api->Config->TemporaryFolder = $this->folder;
	        $api->Config->BaseUrl = 'https://api.sandbox.mangopay.com';

	        $naturalUserResult = $this->getOrCreateUser($api, $user);

	        $session->set("naturalUserId", $naturalUserResult->Id);

		}catch(\MangoPay\Libraries\ResponseException $e){
			throw new \Exception($e->GetCode()."  -  ".$e->GetMessage());
		}catch(MangoPay\Libraries\Exception $e) {
			throw new \Exception($e->GetMessage());
		}


		try{

			$cardRegister = new CardRegistration();
	        $cardRegister->UserId = $naturalUserResult->Id;
	        $cardRegister->Currency = $this->currency;
	        // $cardRegister->CardType = "visa";
	        $createdCardRegister = $api->CardRegistrations->Create($cardRegister);

	        $session->set("cardRegisterId", $createdCardRegister->Id);

	        return $createdCardRegister;
		}catch(\Exception $e){
			throw new \Exception($e->GetCode()."  -  ".$e->GetMessage());
		}

		
	}


	private function getOrCreateUser($api, $user){


		if(is_null($user->getProfile()->getUserPaie())){
			// create
			try{

				// create
				$naturalUser = new UserNatural();
		        $naturalUser->Email = $user->getEmail();
		        $naturalUser->FirstName = $user->getPrenom();
		        $naturalUser->LastName = $user->getNom();
		        $naturalUser->Birthday = 121271;
		        $naturalUser->Nationality = "FR";
		        $naturalUser->CountryOfResidence = "FR";
		        $naturalUserResult = $api->Users->Create($naturalUser);

		        // save

		        $user->getProfile()->setUserPaie($naturalUserResult->Id);
		        $this->em->persist($user);
		        $this->em->flush();

		        return $naturalUserResult;

			}catch(\MangoPay\Libraries\ResponseException $e){
				throw new \Exception($e->GetCode()."  getOrCreateUser  -  ".$e->GetMessage());
			}catch(MangoPay\Libraries\Exception $e) {
				throw new \Exception($e->GetMessage());
			}
		}else{
			// get user
			try{
				$id = $user->getProfile()->getUserPaie();
				$naturalUserResult = $api->Users->Get($id);
				return $naturalUserResult;
			}catch(\MangoPay\Libraries\ResponseException $e){
				throw new \Exception($e->GetCode()."  -  ".$e->GetMessage());
			}catch(MangoPay\Libraries\Exception $e) {
				throw new \Exception($e->GetMessage());
			}
		}
	}


	private function getOrCreateWallet(){

		$errors = array('02625'=>"Invalid card number", '02626'=>"Invalid date. Use mmdd format", '02627'=>"Invalid CCV number",
            '02628'=>"Transaction refused", '02101'=>"Internal Error", '02632'=>"Method GET is not allowed", '09102'=>"Account is locked or inactive",
            '01902'=>"This card is not active", '02624'=>"Card expired", '09104'=>"Client certificate is disabled", '09201'=>"You do not have permissions to make this API call",
            '02631'=>"Delay exceeded");

		$user = $this->user;
		$api = $this->api;
		$session = $this->session;


		// création carte
		// echo $session->get('cardRegisterId');

		try{
            $cardRegister = $api->CardRegistrations->Get($session->get('cardRegisterId'));
            if(is_null($cardRegister->RegistrationData)){
           		$cardRegister->RegistrationData = !is_null($this->requestObj->get('data')) ? 'data=' . $this->requestObj->get('data') : 'errorCode=' . $this->requestObj->get('errorCode');
            	$updatedCardRegister = $api->CardRegistrations->Update($cardRegister);
            }else{
            	$updatedCardRegister = $cardRegister;
            }

        }catch(\MangoPay\Libraries\ResponseException $e){
			throw new \Exception($e->GetCode()."  -  ".$e->GetMessage());
		}catch(MangoPay\Libraries\Exception $e) {
			throw new \Exception($e->GetMessage());
		}

		if ($updatedCardRegister->Status != \MangoPay\CardRegistrationStatus::Validated || !isset($updatedCardRegister->CardId)){
            if($this->requestObj->get('errorCode')){
                if(in_array($this->requestObj->get('errorCode'), array_keys($errors)))
                    throw new \Exception($errors[$this->requestObj->get('errorCode')]);
                else
                    throw new \Exception($this->requestObj->get('errorCode'));
            }else
                throw new \Exception($updatedCardRegister->Status);
        }




		if(is_null($user->getProfile()->getWalletPaie())){
			// create
			try{

				$wallet = new Wallet();
	            $wallet->Owners = array( $updatedCardRegister->UserId );
	            $wallet->Currency = $this->currency;
	            $wallet->Description = 'Temporary wallet for payment demo';

	            $createdWallet = $api->Wallets->Create($wallet);

	            // save wallet in profile
	            $user->getProfile()->setWalletPaie($createdWallet->Id);
	            $this->em->persist($user);
	            $this->em->flush();

	            return array($createdWallet, $updatedCardRegister);

			}catch(\MangoPay\Libraries\ResponseException $e){
				throw new \Exception($e->GetCode()."  -  ".$e->GetMessage());
			}catch(MangoPay\Libraries\Exception $e) {
				throw new \Exception($e->GetMessage());
			}
		}else{
			try{
				$id = $user->getProfile()->getWalletPaie();
				$createdWallet = $api->Wallets->Get($id);
				
				return array($createdWallet, $updatedCardRegister);

			}catch(\MangoPay\Libraries\ResponseException $e){
				throw new \Exception($e->GetCode()."  -  ".$e->GetMessage());
			}catch(MangoPay\Libraries\Exception $e) {
				throw new \Exception($e->GetMessage());
			}
			
		}

	}




	private function getOrCreateBankAccount(){
		$api = $this->api;
		$user = $this->user;
		$request = $this->requestObj;

		if(is_null($user->getProfile()->getBankAccount())){
			try{
				$bankAccount = new BankAccount();

				$legalUserId = $this->getOrCreateLegalUser($user)->Id;
				$bankAccount->Type = "IBAN";
				$bankAccount->Tag = "Virement RIB";
				// $bankAccount->UserId = $legalUserId;
				// $bankAccount->Country = "FR";

				$bankAccount->OwnerName = $this->user->getPrenom()." ".$this->user->getNom();

				$bankAccount->OwnerAddress = new Address();
				$bankAccount->OwnerAddress->AddressLine1 = 'Adresse line 1';
				$bankAccount->OwnerAddress->City = 'France';
				$bankAccount->OwnerAddress->Country = 'FR';
				$bankAccount->OwnerAddress->PostalCode = '11222';



				if(is_null($request->request->get("ibanNumber")) || is_null($request->request->get("bicNumber")))
					throw new \Exception("Pas de code IBAN et/ou BIC");

				$bankAccount->Details = new BankAccountDetailsIBAN();
				$bankAccount->Details->IBAN = $request->request->get("ibanNumber");
				$bankAccount->Details->BIC = $request->request->get("bicNumber");

				Logs::Debug('CREATED Bank Account', $bankAccount);

				$result = $api->Users->CreateBankAccount($legalUserId, $bankAccount);

				Logs::Debug('RESULT Bank Account', $result);

				// save bankAccount
				$user->getProfile()->setBankAccount($result->Id);
	            $this->em->flush();

				return $result;


			}catch(\MangoPay\Libraries\ResponseException $e){
				throw new \Exception($e->GetCode()."  getOrCreateBankAccount  -  ".$e->GetMessage());
			}catch(MangoPay\Libraries\Exception $e) {
				throw new \Exception("getOrCreateBankAccount ".$e->GetMessage());
			}


		}else{
			$bankId = $user->getProfile()->getBankAccount();
			try{
				$legalUserId = $this->getOrCreateLegalUser($user)->Id;
				$result = $api->Users->GetBankAccount($legalUserId, $bankId);
				return $result;
			}catch(\MangoPay\Libraries\ResponseException $e){
				throw new \Exception($e->GetCode()." getOrCreateBankAccount  -  ".$e->GetMessage());
			}catch(MangoPay\Libraries\Exception $e) {
				throw new \Exception("getOrCreateBankAccount ".$e->GetMessage());
			}
			
		}
	}













	public function createPaiementClient(){

		$session = $this->session;
		$reservation = $session->get("reservation");

		$api = new MangoPayApi();
        $api->Config->ClientId = $this->clientId;
        $api->Config->ClientPassword = $this->clefApi;
        $api->Config->TemporaryFolder = $this->folder;
        $this->api = $api;




        try{
			

        	list($createdWallet, $updatedCardRegister)  = $this->getOrCreateWallet();


            Logs::Debug('CREATED Wallet', $createdWallet);


            $card = $api->Cards->Get($updatedCardRegister->CardId);

            // Débite le client
            $payIn = new PayIn();
            $payIn->CreditedWalletId = $createdWallet->Id;
            $payIn->AuthorId = $updatedCardRegister->UserId;
            $payIn->DebitedFunds = new Money();
            $payIn->DebitedFunds->Amount = ($reservation->getPrix()*100) + $this->getPartEntreprise();


            // Honoraire


            $payIn->DebitedFunds->Currency = $this->currency;
            $payIn->Fees = new Money();
            $payIn->Fees->Amount = $this->getPartEntreprise();
            $payIn->Fees->Currency = $this->currency;


            // paiement par carte
            $payIn->PaymentDetails = new PayInPaymentDetailsCard();
            $payIn->PaymentDetails->CardType = $card->CardType;
            $payIn->PaymentDetails->CardId = $card->Id;

            //paiement direct
            $payIn->ExecutionDetails = new PayInExecutionDetailsDirect();
            $payIn->ExecutionDetails->SecureModeReturnURL = $this->url;


            // create Pay-In
            $createdPayIn = $api->PayIns->Create($payIn);

            Logs::Debug('CREATED PayIn', $createdPayIn);

            return array(
            	"createdPayIn"=>$createdPayIn, 
            	"createdWallet"=>$createdWallet, 
            	"card"=>$card);

		}catch(\MangoPay\Libraries\ResponseException $e){
			throw new \Exception($e->GetCode()." createPaiementClient -  ".$e->GetMessage());
		}catch(MangoPay\Libraries\Exception $e) {
			throw new \Exception($e->GetMessage());
		}



	}












	public function createPaiementFournisseur(){

		$session = $this->session;
		$reservation = $session->get("reservation");

		$api = new MangoPayApi();
        $api->Config->ClientId = $this->clientId;
        $api->Config->ClientPassword = $this->clefApi;
        $api->Config->TemporaryFolder = $this->folder;
        $this->api = $api;

        try{
			
        	$legaUserId = $this->getOrCreateLegalUser($this->user)->Id;
            $clientWalletId = $this->paiement->getWallet();
            
            $bankAccount = $this->getOrCreateBankAccount();
            Logs::Debug('CREATED BankAccount', $bankAccount);

            // Crédite le fournisseur
            $payOut = new PayOut();

            $payOut->AuthorId = $legaUserId;
            $payOut->DebitedWalletId = $clientWalletId;
            $payOut->DebitedFunds = new Money();
            $payOut->DebitedFunds->Currency = $this->currency;
            $payOut->DebitedFunds->Amount = $reservation->getPrix()*100;

            $payOut->Fees = new Money();
			$payOut->Fees->Currency = "EUR";
			$payOut->Fees->Amount = 0;

            $payOut->PaymentType = \MangoPay\PayOutPaymentType::BankWire;
            $payOut->MeanOfPaymentDetails = new PayOutPaymentDetailsBankWire();
            $payOut->MeanOfPaymentDetails->BankAccountId = $bankAccount->Id;

            Logs::Debug('CREATED PayOut', $payOut);

            $createdPayOut = $api->PayOuts->Create($payOut);

            Logs::Debug('CREATED PayOut', $createdPayOut);

            // sauvegarde paiement effectuer

            $paiement = $this->paiement;
            $paiement->setEnLigneRegler(true);
            $this->em->persist($paiement);
            $this->em->flush();

            return $createdPayOut;

		}catch(\MangoPay\Libraries\ResponseException $e){
			throw new \Exception($e->GetCode()."  createPaiementFournisseur  -  ".$e->GetMessage());
		}catch(MangoPay\Libraries\Exception $e) {
			throw new \Exception("createPaiementFournisseur  ".$e->GetMessage());
		}



	}



	public function getTransaction(){
		$session = $this->session;
		$reservation = $session->get("reservation");
		$transactionId = $this->requestObj->get("transactionId");

		$api = new MangoPayApi();
        $api->Config->ClientId = $this->clientId;
        $api->Config->ClientPassword = $this->clefApi;
        $api->Config->TemporaryFolder = $this->folder;
        $this->api = $api;


        try{
        	$createdPayIn = $api->PayIns->Get($transactionId);
        	// $cardRegister = $api->CardRegistrations->Get($createdPayIn->PaymentDetails->CardId);
        	$cardRegister = $api->CardRegistrations->Get($session->get('cardRegisterId'));
        	Logs::Debug('CREATED Card', $cardRegister);

        	$walletId = $createdPayIn->CreditedWalletId;
        	$createdWallet = $api->Wallets->Get($walletId);

        	// Logs::Debug('CREATED Wallet', $createdWallet);

        	return array(
            	"createdPayIn"=>$createdPayIn, 
            	"createdWallet"=>$createdWallet, 
            	"card"=>$cardRegister);


        }catch(\MangoPay\Libraries\ResponseException $e){
			throw new \Exception($e->GetCode()."  -  ".$e->GetMessage());
		}catch(MangoPay\Libraries\Exception $e) {
			throw new \Exception($e->GetMessage());
		}

		return $payIn;

	}



	public function annulationPaiement(){
		$session = $this->session;
		$paiement = $this->paiement;

		$api = new MangoPayApi();
        $api->Config->ClientId = $this->clientId;
        $api->Config->ClientPassword = $this->clefApi;
        $api->Config->TemporaryFolder = $this->folder;
        $this->api = $api;

        try{
        	return;




        }catch(\MangoPay\Libraries\ResponseException $e){
			throw new \Exception($e->GetCode()."  -  ".$e->GetMessage());
		}catch(MangoPay\Libraries\Exception $e) {
			throw new \Exception($e->GetMessage());
		}
	}
























	##### Partie Admin / Distribution des fees





	private function getOrCreateAdminUser($user){
		$session = $this->session;
		$em = $this->em;

		$users = $em->getRepository("AdminBundle:userAdmin")->findBy(array("email"=>$user['email']), array(), 1); 
		if(!$user || count($users)==0){
			// create
			$User = new userAdmin();
			$User->setNom($user['nom'])
				->setPrenom($user['prenom'])
				->setEmail($user['email'])
				->setDateNaissance($user['dateNaissance'])
				->setAuteur($this->user);

			$em->commit($User);
			$em->flush();

			return $User;

		}

		return $users[0];

		
		
	}







	private function getOrCreateLegalUser($user){
		$session = $this->session;

		$api = new MangoPayApi();
        $api->Config->ClientId = $this->clientId;
        $api->Config->ClientPassword = $this->clefApi;
        $api->Config->TemporaryFolder = $this->folder;
        $this->api = $api;


        try{

        	// $user = $this->getOrCreateAdminUser($userArray);

        	if(!$user->getProfile()->getUserLegal()){

        		$User = new UserLegal();
        		$User->LegalPersonType = \MangoPay\LegalPersonType::Business;
				$User->Name = $user->getNom()." ".$user->getPrenom();
        		$User->Email = $user->getEmail();

        		$User->LegalRepresentativeEmail = $user->getEmail();
        		$User->LegalRepresentativeEmail = $user->getEmail();
				$User->LegalRepresentativeFirstName = $user->getNom();
				$User->LegalRepresentativeLastName = $user->getPrenom();
				$date = new \Datetime();
				$date->setDate($user->getAnneeNaissance(),1,1);
				$User->LegalRepresentativeBirthday = $date->getTimestamp();
				$User->LegalRepresentativeNationality = "FR";
				$User->LegalRepresentativeCountryOfResidence = "FR";

				$result = $api->Users->Create($User);

				Logs::Debug('CREATED User', $result);

				$user->getProfile()->setUserLegal($result->Id);
				$this->em->flush();
				return $result;


        	}else{
        		$uid = $user->getProfile()->getUserLegal();
        		$User = $api->Users->Get($uid);
        		return $User;
        	}
        		

        }catch(\MangoPay\Libraries\ResponseException $e){
			throw new \Exception($e->GetCode()." getOrCreateLegalUser -  ".$e->GetMessage());
		}catch(MangoPay\Libraries\Exception $e) {
			throw new \Exception("getOrCreateLegalUser ".$e->GetMessage());
		}
	}



	private function getOrCreateLegalWallet($userArray){
		$session = $this->session;
		$reservation = $session->get("reservation");

		$api = new MangoPayApi();
        $api->Config->ClientId = $this->clientId;
        $api->Config->ClientPassword = $this->clefApi;
        $api->Config->TemporaryFolder = $this->folder;
        $this->api = $api;

        try{

        	$user = $this->getOrCreateLegalUser($userArray['email']);
        	if(!$user->getWalletId()){

        		$wallet = new Wallet();
	            $wallet->Owners = array($user->Id);
	            $wallet->Currency = $this->currency;
	            $wallet->Description = 'Wallet for Admin';

	            $createdWallet = $api->Wallets->Create($wallet);

        	}else{

        		$wid = $user->getWalletId();
        		$createdWallet = $api->Wallets->Get($wid);
        		return $createdWallet;
        	}

        }catch(\MangoPay\Libraries\ResponseException $e){
			throw new \Exception($e->GetCode()."  -  ".$e->GetMessage());
		}catch(MangoPay\Libraries\Exception $e) {
			throw new \Exception($e->GetMessage());
		}
	}





	public function getFees(){
		$session = $this->session;
		$paiement = $this->paiement;

		$api = new MangoPayApi();
        $api->Config->ClientId = $this->clientId;
        $api->Config->ClientPassword = $this->clefApi;
        $api->Config->TemporaryFolder = $this->folder;
        $this->api = $api;

        try{

        	// $UserLegal = $this->getOrCreateLegalUser($this->userDevelopper);

        	$clientFees = $api->Clients->Get("FEES_EUR");

        	Logs::Debug('GET Client', $clientFees);
        	// Logs::Debug('GET Client Wallets', $clientFees);

        	$createdWallet = $api->Users->Get("Fees wallet");

        	Logs::Debug('GET User', $createdWallet);

        	die();
        }catch(\MangoPay\Libraries\ResponseException $e){
			throw new \Exception($e->GetCode()."  -  ".$e->GetMessage());
		}catch(MangoPay\Libraries\Exception $e) {
			throw new \Exception($e->GetMessage());
		}



	}

}