angular.module('messages.services', [])
	.service('MessageService', ['$q', '$http', 'UserService' , function($q, $http, UserService){

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
				if(currentUser){
					initialized=true;
					return $q.when(currentUser);
				}else if(UserService.currentUser){
					currentUser = UserService.currentUser;
					initialized=true;
					return $q.when(currentUser);
				}else{
					currentUser = UserService.getMe().then(function(user){
						currentUser = user;
						initialized=true;
						$q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					})
				}

			},


			addFromAnnonce:function(message, annonce){
				data = {message:message, user:currentUser, annonce:annonce, _format:'json'};
				return $http.post("/api/messagerie/addFromAnnonce", data).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},


			addFromThread:function(message, thread){
				data = {message:message, user:currentUser, thread:thread, _format:'json'};
				return $http.post("/api/messagerie/addFromThread", data).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

			delFromAnnonce:function(annonce, message, messageId){
				data = {annonce:annonce, message:message, messageId:messageId, user:currentUser, _format:'json'};
				return $http.post("/api/messagerie/delFromAnnonce", data).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

			del:function(messageId){
				data = {messageId:messageId, user:currentUser, _format:'json'};
				return $http.post("/api/messagerie/del", data).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

			getFromAnnonce:function(annonce){
				return $http.post("/api/messagerie/get", {annonce:annonce, type:'annonce', _format:'json'}).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				});
			},

			getAll:function(){
				return $http.post("/api/messagerie/get", {type:'all', _format:'json'}).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				});
			},

			getSended:function(){
				return $http.post("/api/messagerie/get", {type:'sended', _format:'json'}).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				});
			},

			getDeleted:function(){
				return $http.post("/api/messagerie/get", {type:'deleted', _format:'json'}).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				});
			},

		}



	}])

	.service('AvisService', ['$q', '$http', 'UserService' , function($q, $http, UserService){
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
				if(currentUser){
					initialized=true;
					return $q.when(currentUser);
				}else if(UserService.currentUser){
					currentUser = UserService.currentUser;
					initialized=true;
					return $q.when(currentUser);
				}else{
					currentUser = UserService.getMe().then(function(user){
						currentUser = user;
						initialized=true;
						$q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					})
				}

			},


			getAll:function(userId){
				return $http.post("/api/avis/getAll", {userId:userId, _format:"json"}).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			},

			send:function(userId, message, note){
				data = {user:currentUser, userId:userId, message:message, note:note, _format:'json'};
				return $http.post("/api/avis/add", data).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			}
		}

	}]);