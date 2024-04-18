myApp.controller("reservationCtrl", ['$scope', 'AnnonceService', 'ReservationService', function($scope, AnnonceService, ReservationService){

	$scope.reservation = {};
	$scope.annonce = {};
  	$scope.user = {};

	$scope.getAnnonce = function(id){
		AnnonceService.init().then(function(user){
			$scope.user = user;
			AnnonceService.get(id).then(function(annonce){
				$scope.annonce = annonce;
			},function(err){
        		console.log(err);
	      	})
	    }, function(err){
	      console.log(err);
	    })
	};


	$scope.getReservation = function(reservationId){
		ReservationService.init().then(function(user){
			$scope.user = user;
			ReservationService.get(reservationId).then(function(reservation){
				$scope.reservation = reservation;
				console.log(reservation);
			},function(err){
        		console.log(err);
	      	})
	    }, function(err){
	      console.log(err);
	    })
	};


	$scope.validReservation = function(reservationId, value){
		ReservationService.init().then(function(user){
			$scope.user = user;
			ReservationService.valider(reservationId, value).then(function(reservation){
				$scope.reservation = reservation;
				console.log(reservation);
			},function(err){
        		console.log(err);
	      	})
	    }, function(err){
	      console.log(err);
	    })
	};


	$scope.getTotal = function(){
		if($scope.reservation.TypeCout=="Par kilomètres")
	      return $scope.reservation.Prix * $scope.reservation.Distance;
	    else if($scope.reservation.TypeCout=="Par heures")
	      return Math.round($scope.reservation.Prix * $scope.reservation.Duree/3600, -2);
	    else if($scope.reservation.TypeCout=="Par places")
	      return $scope.reservation.Prix * $scope.reservation.NbPlaces;
	    else if($scope.reservation.TypeCout=="Prix unique")
	      return $scope.reservation.Prix;
	    else
	      return $scope.reservation.Prix * $scope.reservation.NbPlaces;
	}

}])


















.controller("devisCtrl", ['$scope', 'AnnonceService', 'DevisService', function($scope, AnnonceService, DevisService){

	$scope.annonce = {};
  	$scope.user = {};
  	$scope.devis = {};

	$scope.getAnnonce = function(id){
		AnnonceService.init().then(function(user){
			$scope.user = user;
			AnnonceService.get(id).then(function(annonce){
				$scope.annonce = annonce;
			},function(err){
        		console.log(err);
	      	})
	    }, function(err){
	      console.log(err);
	    })
	}


	$scope.getDevis = function(devisId){
		DevisService.init().then(function(user){
			$scope.user = user;
			DevisService.get(devisId).then(function(devis){
				console.log(devis);
				$scope.devis = devis;
			},function(err){
        		console.log(err);
	      	})
	    }, function(err){
	      console.log(err);
	    })
	};


	$scope.accepter = function(devisId, value){
		DevisService.init().then(function(user){
			$scope.user = user;
			DevisService.accepter(devisId, value).then(function(devis){
				$scope.devis = devis;
			},function(err){
        		console.log(err);
	      	})
	    }, function(err){
	      console.log(err);
	    })
	};


	$scope.refuser = function(devisId, value){
		DevisService.init().then(function(user){
			$scope.user = user;
			DevisService.refuser(devisId, value).then(function(devis){
				$scope.devis = devis;
			},function(err){
        		console.log(err);
	      	})
	    }, function(err){
	      console.log(err);
	    })
	};


	$scope.getTotal = function(){
		console.log($scope.devis);
		if($scope.devis.TypeDevis=="Par kilomètres")
	      return Math.round($scope.devis.PrixUnitaire * $scope.devis.Distance/1000, -2);
	    else if($scope.devis.TypeDevis=="Par heures")
	      return Math.round($scope.devis.PrixUnitaire * $scope.devis.Duree/3600, -2);
	    else if($scope.devis.TypeDevis=="Par places")
	      return $scope.devis.PrixUnitaire * $scope.devis.NbPlaces;
	    else if($scope.devis.TypeDevis=="Prix unique")
	      return $scope.devis.PrixUnitaire;
	    else
	      return $scope.devis.Prix * $scope.devis.NbPlaces;
	}



}]);