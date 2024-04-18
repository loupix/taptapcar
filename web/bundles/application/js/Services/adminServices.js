'use strict';

angular.module('admin.services', [])


	.service('AdminService', ['$q', '$http', 'UserService', function($q, $http, UserService){

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

			getUsers:function(params){
				return $http.post("/api/admin/users", params).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			},

			getUserInfo:function(params){
				return $http.post("/api/admin/user/info", params).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			},


			delUser:function(params){
				return $http.post("/api/admin/user/del", params).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			},

			changeUser:function(params){
				return $http.post("/api/admin/user/change", params).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			},

			verifieUser:function(params){
				return $http.post("/api/admin/user/verifie", params).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			},

			findUser:function(terme){
				return $http.post("/api/admin/user/find", {terme:terme, _format:"json"}).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			},


			getAnnonces:function(params){
				return $http.post("/api/admin/annonces", params).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			},


			delAnnonce:function(params){
				return $http.post("/api/admin/annonce/del", params).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			},

			activeAnnonce:function(params){
				return $http.post("/api/admin/annonce/actif", params).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			},


			getReservations:function(params){
				return $http.post("/api/admin/reservations", params).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			},


			getPaiements:function(params){
				return $http.post("/api/admin/paiements", params).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			},


			getContacts:function(params){
				return $http.post("/api/admin/contacts", params).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			},

			getErreurs:function(params){
				return $http.post("/api/admin/erreurs", params).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			},

			getParts:function(params){
				return $http.post("/api/admin/fees/parts", params).then(function(rep){
					return $q.when(rep.data);
				}, function(err){
					return $q.reject(err);
				});
			}

		}

	}]);