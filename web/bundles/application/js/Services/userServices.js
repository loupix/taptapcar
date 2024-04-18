angular.module('user.services', [])
	.service('UserService', ['$q', '$http', '$geolocation', function($q, $http, $geolocation){

		var currentUser = {
			Id:false,
			Nom:"",
			Prenom:"",
			Email:"",
			Type:'visitor',
			Profile:{},
			location:{}
		};

		return{
			getMe : function(){
				return $http.post("/api/user/me", {_format:'json'}).then(function(response){
					currentUser.Id = response.data.Id;
					currentUser.Nom = response.data.Nom;
					currentUser.Prenom = response.data.Prenom;
					currentUser.Email = response.data.Email;
					currentUser.Profile = response.data.Profile;
					currentUser.Type = "user";
					return $q.when(currentUser);
				}, function(err){
					if(err.status==404)
						return $q.reject("Pas d'user");
					
					return $q.reject(err);
				})
			},


			getPosition:function(){
				return $geolocation.getCurrentPosition().then(function(position) {
					return $q.when(position);
				}, function(err){
					return $q.reject(err);
				});
			},


			get: function(userId){
				return $http.post("/api/user/get", {uid:userId, _format:'json'}).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			}
		};

	}]);