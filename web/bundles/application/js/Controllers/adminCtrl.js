'use strict';

myApp.controller("adminUsersCtrl", ['$scope','AdminService', function($scope, AdminService){



	// Pagination
	$scope.totalItems = 0;
	$scope.currentPage = 0;
	$scope.maxSize = 5;

	$scope.setPage = function (pageNo) {
		$scope.currentPage = pageNo;
	};

	$scope.getMinPage = function(){
		if($scope.currentPage * $scope.maxSize > $scope.totalItems)
		  return $scope.totalItems;
		else
		  return $scope.currentPage * $scope.maxSize;
	}

	$scope.getMaxPage = function(){
		if(($scope.currentPage+1) * $scope.maxSize < $scope.totalItems)
		  return ($scope.currentPage+1) * $scope.maxSize;
		else
		  return $scope.totalItems;
	}

	$scope.pageChanged = function() {
		$scope.params._page = $scope.currentPage -1;
		$scope.loadUser();
	};




	$scope.users = [];

	$scope.params = {
		type:"Users",
		user:null,
		_size:$scope.maxSize,
		_page:$scope.currentPage,
		_order:"DESC",
		_format:"json"
	}

	$scope.loadUser = function(){
		AdminService.init().then(function(user){
			$scope.params.user=user;
			AdminService.getUsers($scope.params).then(function(data){
				$scope.users=data.users;
				$scope.totalItems=data.maxResult;
			}, function(err){
				console.log(err);
			})
		}, function(err){
			console.log(err);
		});
	}


	$scope.delUser = function(uid){
		AdminService.init().then(function(user){
			$scope.params.user=user;
			var params = angular.copy($scope.params);
			params.uid = uid;
			AdminService.delUser(params).then(function(data){
				$scope.users=data.users;
				$scope.totalItems=data.maxResult;
			}, function(err){
				console.log(err);
			})
		}, function(err){
			console.log(err);
		});
	}


	$scope.changeUser = function(uid){
		AdminService.init().then(function(user){
			$scope.params.user=user;
			var params = angular.copy($scope.params);
			params.uid = uid;
			AdminService.changeUser(params).then(function(data){
				$scope.users=data.users;
				$scope.totalItems=data.maxResult;
			}, function(err){
				console.log(err);
			})
		}, function(err){
			console.log(err);
		});
	}


	$scope.verifieUser = function(uid){
		AdminService.init().then(function(user){
			$scope.params.user=user;
			var params = angular.copy($scope.params);
			params.uid = uid;
			AdminService.verifieUser(params).then(function(data){
				$scope.users=data.users;
				$scope.totalItems=data.maxResult;
			}, function(err){
				console.log(err);
			})
		}, function(err){
			console.log(err);
		});
	}

	$scope.rechercheUser = function(){
		AdminService.init().then(function(user){
			$scope.params.user=user;
			var terme = angular.copy($scope.termeRecherche);
			AdminService.findUser(terme).then(function(data){
				$scope.users=data.users;
				$scope.totalItems=data.users.length();
			});
		});
	}


}])


.controller("adminAnnoncesCtrl", ['$scope','AdminService', function($scope, AdminService){
	

	// Pagination
	$scope.totalItems = 0;
	$scope.currentPage = 0;
	$scope.maxSize = 5;


	$scope.setPage = function (pageNo) {
		$scope.currentPage = pageNo;
	};

	$scope.getMinPage = function(){
		if($scope.currentPage * $scope.maxSize > $scope.totalItems)
		  return $scope.totalItems;
		else
		  return $scope.currentPage * $scope.maxSize;
	}

	$scope.getMaxPage = function(){
		if(($scope.currentPage+1) * $scope.maxSize < $scope.totalItems)
		  return ($scope.currentPage+1) * $scope.maxSize;
		else
		  return $scope.totalItems;
	}

	$scope.pageChanged = function() {
		$scope.params._page = $scope.currentPage-1;
		$scope.loadAnnonces();
	};

	


	$scope.annonces = [];

	$scope.params = {
		type:"Users",
		user:null,
		_size:$scope.maxSize,
		_page:$scope.currentPage,
		_order:"DESC",
		_format:"json"
	}

	$scope.loadAnnonces = function(){
		AdminService.init().then(function(user){
			$scope.params.user=user;
			AdminService.getAnnonces($scope.params).then(function(data){
				$scope.annonces=data.annonces;
				$scope.totalItems=data.maxResult;
			}, function(err){
				console.log(err);
			})
		}, function(err){
			console.log(err);
		})
	}


	$scope.delAnnonce = function(aid){
		console.log(aid);
		AdminService.init().then(function(user){
			$scope.params.user=user;
			var params = angular.copy($scope.params);
			params.aid = aid;
			AdminService.delAnnonce(params).then(function(data){
				$scope.annonces=data.annonces;
				$scope.totalItems=data.maxResult;
			}, function(err){
				console.log(err);
			})
		}, function(err){
			console.log(err);
		})
	}


	$scope.activeAnnonce = function(aid){
		console.log(aid);
		AdminService.init().then(function(user){
			$scope.params.user=user;
			var params = angular.copy($scope.params);
			params.aid = aid;
			AdminService.activeAnnonce(params).then(function(data){
				$scope.annonces=data.annonces;
				$scope.totalItems=data.maxResult;
			}, function(err){
				console.log(err);
			})
		}, function(err){
			console.log(err);
		})
	}

}])


.controller("adminReservationsCtrl", ['$scope','AdminService', function($scope, AdminService){


	// Pagination
	$scope.totalItems = 0;
	$scope.currentPage = 0;
	$scope.maxSize = 5;

	$scope.setPage = function (pageNo) {
		$scope.currentPage = pageNo;
	};

	$scope.getMinPage = function(){
		if($scope.currentPage * $scope.maxSize > $scope.totalItems)
		  return $scope.totalItems;
		else
		  return $scope.currentPage * $scope.maxSize;
	}

	$scope.getMaxPage = function(){
		if(($scope.currentPage+1) * $scope.maxSize < $scope.totalItems)
		  return ($scope.currentPage+1) * $scope.maxSize;
		else
		  return $scope.totalItems;
	}

	$scope.pageChanged = function() {
		$scope.params._page = $scope.currentPage -1;
		$scope.loadUser();
	};


	$scope.reservations = [];

	$scope.params = {
		type:"Users",
		user:null,
		_size:$scope.maxSize,
		_page:$scope.currentPage,
		_order:"DESC",
		_format:"json"
	}

	$scope.loadReservations = function(){
		AdminService.init().then(function(user){
			$scope.params.user=user;
			AdminService.getReservations($scope.params).then(function(data){
				$scope.reservations=data.reservations;
				$scope.totalItems=data.maxResult;
			}, function(err){
				console.log(err);
			})
		}, function(err){
			console.log(err);
		})
	}


}])


.controller("adminPaiementsCtrl", ['$scope','AdminService', function($scope, AdminService){
	
	// Pagination
	$scope.totalItems = 0;
	$scope.currentPage = 0;
	$scope.maxSize = 5;

	$scope.setPage = function (pageNo) {
		$scope.currentPage = pageNo;
	};

	$scope.getMinPage = function(){
		if($scope.currentPage * $scope.maxSize > $scope.totalItems)
		  return $scope.totalItems;
		else
		  return $scope.currentPage * $scope.maxSize;
	}

	$scope.getMaxPage = function(){
		if(($scope.currentPage+1) * $scope.maxSize < $scope.totalItems)
		  return ($scope.currentPage+1) * $scope.maxSize;
		else
		  return $scope.totalItems;
	}

	$scope.pageChanged = function() {
		$scope.params._page = $scope.currentPage -1;
		$scope.loadUser();
	};


	$scope.paiements = [];

	$scope.params = {
		type:"Users",
		user:null,
		_size:$scope.maxSize,
		_page:$scope.currentPage,
		_order:"DESC",
		_format:"json"
	}

	$scope.loadPaiements = function(){
		AdminService.init().then(function(user){
			$scope.params.user=user;
			AdminService.getPaiements($scope.params).then(function(data){
				$scope.paiements=data.paiements;
				$scope.totalItems=data.maxResult;
			}, function(err){
				console.log(err);
			})
		}, function(err){
			console.log(err);
		})
	}

}])


.controller("adminContactsCtrl", ['$scope','AdminService', function($scope, AdminService){

	// Pagination
	$scope.totalItems = 0;
	$scope.currentPage = 0;
	$scope.maxSize = 5;

	$scope.setPage = function (pageNo) {
		$scope.currentPage = pageNo;
	};

	$scope.getMinPage = function(){
		if($scope.currentPage * $scope.maxSize > $scope.totalItems)
		  return $scope.totalItems;
		else
		  return $scope.currentPage * $scope.maxSize;
	}

	$scope.getMaxPage = function(){
		if(($scope.currentPage+1) * $scope.maxSize < $scope.totalItems)
		  return ($scope.currentPage+1) * $scope.maxSize;
		else
		  return $scope.totalItems;
	}

	$scope.pageChanged = function() {
		$scope.params._page = $scope.currentPage -1;
		$scope.loadUser();
	};


	$scope.contacts = [];

	$scope.params = {
		type:"Users",
		user:null,
		_size:$scope.maxSize,
		_page:$scope.currentPage,
		_order:"ASC",
		_format:"json"
	}

	$scope.loadContacts = function(){
		AdminService.init().then(function(user){
			$scope.params.user=user;
			AdminService.getContacts($scope.params).then(function(data){
				$scope.contacts=data.contacts;
				$scope.totalItems=data.maxResult;
			}, function(err){
				console.log(err);
			})
		}, function(err){
			console.log(err);
		})
	};


	$scope.delMessage = function(cid){
		AdminService.init().then(function(user){
			$scope.params.user=user;
			var params = angular.copy($scope.params);
			params.cid = cid;

			AdminService.delContacts($scope.params).then(function(data){
				$scope.contacts=data.contacts;
				$scope.totalItems=data.maxResult;
			}, function(err){
				console.log(err);
			})
		}, function(err){
			console.log(err);
		})
	}


}])



.controller("adminFeesCtrl", ['$scope','AdminService', function($scope, AdminService){


	$scope.params = {
		type:"Users",
		user:null,
		_order:"ASC",
		_format:"json"
	}

	$scope.parts = {};


	$scope.init = function(){

		AdminService.init().then(function(user){
			$scope.params.user=user;

			AdminService.getParts($scope.params).then(function(parts){
				$scope.parts = parts;
			}, function(err){
				console.log(err);
			})
		}, function(err){
			console.log(err);
		})
	}

}])


.controller("adminErreursCtrl", ['$scope','AdminService', function($scope, AdminService){


}])
