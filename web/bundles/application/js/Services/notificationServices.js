angular.module('notificatons.services', [])
	.service('NotificationsService', ['$q', '$http', 'UserService' , function($q, $http, UserService){

		var initialized = false;


		var currentUser = {
			Id:0,
			Nom:"",
			Prenom:"",
			Email:"",
			Type:'visitor',
			Profile:{},
			location:{}
		};


		return{

			init:function(){
				if(UserService.currentUser){
					currentUser = UserService.currentUser;
					return $q.when(currentUser);
				}else{
					return currentUser = UserService.getMe().then(function(user){
						currentUser = user;
						initialized=true;
						$q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					})
				}

			},


			getToMe:function(){
				return $http.post("/api/notification/getAll", {_format:'json', uid:currentUser.Id}).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				});
			}

		}



	}]);