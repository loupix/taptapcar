myApp.controller("userCtrl", ['$scope', '$http', '$geolocation', '$interval',
		'UserService', 'NotificationsService', function($scope, $http, $geolocation, $interval, UserService, NotificationsService){


	$scope.me = {
		Id:0,
		Nom:"",
		Prenom:"",
		Email:"",
		Profile:{},
		location:{}
	};

	$scope.notifications = [];

	$scope.getMe = function(){
		UserService.getPosition().then(function(location){
			$scope.me.location = location;
		}, function(err){
			console.error(err);
		});


		UserService.getMe().then(function(user){

			$scope.me.Id = user.Id;
			$scope.me.Nom = user.Nom;
			$scope.me.Prenom = user.Prenom;
			$scope.me.Email = user.Email;
			$scope.me.Profile = user.Profile;

			console.log($scope.me);

		}, function(err){
			console.error(err);
		});



	};

	$scope.notifications = [];

	$scope.getNotifications = function(){
		NotificationsService.init().then(function(){
			NotificationsService.getToMe().then(function(notifications){
				$scope.notifications = notifications;
			}, function(err){
				if(err.status!=500) console.log(err);
			});
		}, function(err){
			console.log(err);
		});
		
	};

	// $interval(function(){
	// 	return $scope.getNotifications();
	// }, 500);

	$scope.items = [
		'The first choice!',
		'And another choice for you.',
		'but wait! A third!'
	];


	$scope.toggled = function(open) {
		console.log('Dropdown is now: ', open);
	};



}])


.controller("messagesUserCtrl", ['$scope', '$http', function($scope, $http){
	$scope.threads = {
		sended:[],
		all:[],
		deleted:[]
	};


	$scope.getNew = function(){
		http.post("/api/messagerie/get", {type:'new', _format:'json'}).then(function(response){
			$scope.messages = response.data;
		}, function(err){
			console.error(err);
		});
	};

	$scope.getAll = function(){
		http.post("/api/messagerie/get", {type:'all', _format:'json'}).then(function(response){
			$scope.messages = response.data;
		}, function(err){
			console.error(err);
		});
	};


	$scope.getDeleted = function(){
		http.post("/api/messagerie/get", {type:'deleted', _format:'json'}).then(function(response){
			$scope.messages = response.data;
		}, function(err){
			console.error(err);
		});
	};

}])












.controller("profileCtrl", ['$scope', '$http','Upload', 'UserService', function($scope, $http, Upload, UserService){
	
	$scope.autocompleteOptions = {
	    componentRestrictions: { country: 'fr' },
	    types: ['geocode']
	};

	$scope.place = document.getElementById("userbundle_profile_ville_nom").value;
	$scope.file = "";
	$scope.user = {};

	$scope.getMe = function(){
		UserService.getMe().then(function(user){
			$scope.file = user.Profile.Photo.Src;
			$scope.user = user;
		}, function(err){
			console.log(err);
		});
	};


	$scope.getUser = function(id){
		UserService.get(id).then(function(user){
			$scope.user = user;
		}, function(err){
			console.log(err);
		});
	};



    // upload on file select or drop
    $scope.upload = function (file) {
        Upload.upload({
            url: '/api/user/upload',
            data: {file: file}
        }).then(function (resp) {
            console.log('Success ' + resp.config.data.file.name + ' uploaded. Response: ' + resp.data);
            $scope.file = "/"+resp.data.file;
        }, function (resp) {
            console.log('Error status: ' + resp.status);
        }, function (evt) {
            var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
            console.log('progress: ' + progressPercentage + '% ' + evt.config.data.file.name);
        });
    };


    $scope.submitForm = function(id){
		document.getElementById(id).submit();
	};


	$scope.setPlace = function(user){
		
	}

	$scope.loadPlace = function(){
		var componentForm = {
		    street_number: 'short_name',
		    route: 'long_name',
		    locality: 'long_name',
		    administrative_area_level_1: 'short_name',
		    country: 'long_name',
		    postal_code: 'short_name'
		};

		console.log(place);

		var place = $scope.place;
		// console.log(place);
		document.getElementById('userbundle_profile_ville_nom').value = place.formatted_address;
		document.getElementById('userbundle_profile_ville_latitude').value = place.geometry.location.lat();
		document.getElementById('userbundle_profile_ville_longitude').value = place.geometry.location.lng();
		for (var i = 0; i < place.address_components.length; i++) {
			var addressType = place.address_components[i].types[0];
			if (componentForm[addressType]) {
				var val = place.address_components[i][componentForm[addressType]];
				document.getElementById('userbundle_profile_ville_'+addressType).value = val;
			}
		}
	};

	$scope.initAutocomplete = function(data){
		console.log("initAutocomplete");
		console.log(data);
	};




}])

.controller("profileAvisCtrl", ['$scope', '$http','AvisService', function($scope, $http, AvisService){

	$scope.avis = [];
	$scope.user = false;
	$scope.connected = false;
	$scope.userId = null;
	$scope.loading=false;

	$scope.getAll = function(userId){
		$scope.userId = userId;

		AvisService.init().then(function(user){
			$scope.user = user;
			AvisService.getAll(userId).then(function(avis){
				$scope.avis = avis;
			}, function(err){
				console.log(err);
			})
		}, function(err){
			console.log(err);
		})
	};


	$scope.send = function(){
		var userId = $scope.userId;
		var message = $scope.message;
		var note = $scope.note;

		$scope.message="";
		$scope.note="";
		$scope.loading=true;

		AvisService.send(userId, message, note).then(function(avis){
			$scope.loading=false;
			$scope.avis.unshift(avis);
		}, function(err){
			console.log(err);
		});
	}

}]);