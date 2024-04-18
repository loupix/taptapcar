angular.module('annonce.services', [])


	.service('AnnonceService', ['$q', '$http', 'UserService', 'MessageService' , function($q, $http, UserService, MessageService){

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


		///// Calcule Distance

		function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
			var R = 6371; // Radius of the earth in km
			var dLat = deg2rad(lat2-lat1);  // deg2rad below
			var dLon = deg2rad(lon2-lon1); 
			var a = 
			  Math.sin(dLat/2) * Math.sin(dLat/2) +
			  Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
			  Math.sin(dLon/2) * Math.sin(dLon/2)
			  ; 
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
			var d = R * c; // Distance in km
			return d;
		}

		function deg2rad(deg) {
			return deg * (Math.PI/180);
		}


		return{

			init:function(){
				if(currentUser){
					MessageService.currentUser = currentUser;
					return MessageService.init().then(function(user){
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					});
					
				}else if(UserService.currentUser){
					currentUser = UserService.currentUser;
					MessageService.currentUser = currentUser;
					return MessageService.init().then(function(user){
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					});

				}else{
					return currentUser = UserService.getMe().then(function(user){
						currentUser = user;
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					})
				}

			},

			get:function(annonceId){
				var postData = {id:annonceId, type:'Annonce', user:currentUser, _format:'json'};
				return $http.post("/api/annonce/get", postData).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

			find:function(recherche){
				recherche.type = "Annonce";
				return $http.post("/api/recherche", recherche).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

			sendMessage:function(message, annonce){
				annonce.type = "Annonce";
				return MessageService.addFromAnnonce(message, annonce);
			},

			getMessages:function(annonce){
				annonce.type = "Annonce";
				return MessageService.getFromAnnonce(annonce);
			},

			removeMessage:function(annonce, message, messageId){
				annonce.type = "Annonce";
				return MessageService.delFromAnnonce(annonce, message, messageId);
			}

		}

	}])



	.service('VtcService', ['$q', '$http', 'UserService', 'MessageService' , function($q, $http, UserService, MessageService){

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


		///// Calcule Distance

		function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
			var R = 6371; // Radius of the earth in km
			var dLat = deg2rad(lat2-lat1);  // deg2rad below
			var dLon = deg2rad(lon2-lon1); 
			var a = 
			  Math.sin(dLat/2) * Math.sin(dLat/2) +
			  Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
			  Math.sin(dLon/2) * Math.sin(dLon/2)
			  ; 
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
			var d = R * c; // Distance in km
			return d;
		}

		function deg2rad(deg) {
			return deg * (Math.PI/180);
		}


		return{

			init:function(){
				if(currentUser){
					MessageService.currentUser = currentUser;
					return MessageService.init().then(function(user){
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					});
					
				}else if(UserService.currentUser){
					currentUser = UserService.currentUser;
					MessageService.currentUser = currentUser;
					return MessageService.init().then(function(user){
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					});

				}else{
					return currentUser = UserService.getMe().then(function(user){
						currentUser = user;
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					})
				}

			},

			get:function(annonceId){
				var postData = {id:annonceId, type:'Vtc', user:currentUser, _format:'json'};
				return $http.post("/api/annonce/get", postData).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

			find:function(recherche){
				recherche.type = "Vtc";
				return $http.post("/api/recherche", recherche).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

			sendMessage:function(message, annonce){
				annonce.type = "Vtc";
				return MessageService.addFromAnnonce(message, annonce);
			},

			getMessages:function(annonce){
				annonce.type = "Vtc";
				return MessageService.getFromAnnonce(annonce);
			},

			removeMessage:function(annonce, message, messageId){
				annonce.type = "Vtc";
				return MessageService.delFromAnnonce(annonce, message, messageId);
			},

			isDispo:function(annonceId, disponnible){
				var postData = {id:annonceId, type:'Vtc', user:currentUser, disponnible:disponnible, _format:'json'};
				return $http.post("/api/annonce/disponnible", postData).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

		}

	}])

	.service('TaxiService', ['$q', '$http', 'UserService', 'MessageService' , function($q, $http, UserService, MessageService){

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


		///// Calcule Distance

		function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
			var R = 6371; // Radius of the earth in km
			var dLat = deg2rad(lat2-lat1);  // deg2rad below
			var dLon = deg2rad(lon2-lon1); 
			var a = 
			  Math.sin(dLat/2) * Math.sin(dLat/2) +
			  Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
			  Math.sin(dLon/2) * Math.sin(dLon/2)
			  ; 
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
			var d = R * c; // Distance in km
			return d;
		}

		function deg2rad(deg) {
			return deg * (Math.PI/180);
		}


		return{

			init:function(){
				if(currentUser){
					MessageService.currentUser = currentUser;
					return MessageService.init().then(function(user){
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					});
					
				}else if(UserService.currentUser){
					currentUser = UserService.currentUser;
					MessageService.currentUser = currentUser;
					return MessageService.init().then(function(user){
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					});

				}else{
					return currentUser = UserService.getMe().then(function(user){
						currentUser = user;
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					})
				}

			},

			get:function(annonceId){
				var postData = {id:annonceId, type:'Taxi', user:currentUser, _format:'json'};
				return $http.post("/api/annonce/get", postData).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

			find:function(recherche){
				recherche.type = "Taxi";
				return $http.post("/api/recherche", recherche).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

			sendMessage:function(message, annonce){
				annonce.type = "Taxi";
				return MessageService.addFromAnnonce(message, annonce);
			},

			getMessages:function(annonce){
				annonce.type = "Taxi";
				return MessageService.getFromAnnonce(annonce);
			},

			removeMessage:function(annonce, message, messageId){
				annonce.type = "Taxi";
				return MessageService.delFromAnnonce(annonce, message, messageId);
			},

			isDispo:function(annonceId, disponnible){
				var postData = {id:annonceId, type:'Taxi', user:currentUser, disponnible:disponnible, _format:'json'};
				return $http.post("/api/annonce/disponnible", postData).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

		}

	}])


	.service('CovoiturageService', ['$q', '$http', 'UserService', 'MessageService' , function($q, $http, UserService, MessageService){

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


		///// Calcule Distance

		function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
			var R = 6371; // Radius of the earth in km
			var dLat = deg2rad(lat2-lat1);  // deg2rad below
			var dLon = deg2rad(lon2-lon1); 
			var a = 
			  Math.sin(dLat/2) * Math.sin(dLat/2) +
			  Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
			  Math.sin(dLon/2) * Math.sin(dLon/2)
			  ; 
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
			var d = R * c; // Distance in km
			return d;
		}

		function deg2rad(deg) {
			return deg * (Math.PI/180);
		}


		return{

			init:function(){
				if(currentUser){
					MessageService.currentUser = currentUser;
					return MessageService.init().then(function(user){
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					});
					
				}else if(UserService.currentUser){
					currentUser = UserService.currentUser;
					MessageService.currentUser = currentUser;
					return MessageService.init().then(function(user){
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					});

				}else{
					return currentUser = UserService.getMe().then(function(user){
						currentUser = user;
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					})
				}

			},

			get:function(annonceId){
				var postData = {id:annonceId, type:'Covoiturage', user:currentUser, _format:'json'};
				return $http.post("/api/annonce/get", postData).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

			find:function(recherche){
				recherche.type = "Covoiturage";
				return $http.post("/api/recherche", recherche).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

			sendMessage:function(message, annonce){
				annonce.type = "Covoiturage";
				return MessageService.addFromAnnonce(message, annonce);
			},

			getMessages:function(annonce){
				annonce.type = "Covoiturage";
				return MessageService.getFromAnnonce(annonce);
			},

			removeMessage:function(annonce, message, messageId){
				annonce.type = "Covoiturage";
				return MessageService.delFromAnnonce(annonce, message, messageId);
			}

		}

	}])


	.service('DemenagementService', ['$q', '$http', 'UserService', 'MessageService' , function($q, $http, UserService, MessageService){

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


		///// Calcule Distance

		function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
			var R = 6371; // Radius of the earth in km
			var dLat = deg2rad(lat2-lat1);  // deg2rad below
			var dLon = deg2rad(lon2-lon1); 
			var a = 
			  Math.sin(dLat/2) * Math.sin(dLat/2) +
			  Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
			  Math.sin(dLon/2) * Math.sin(dLon/2)
			  ; 
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
			var d = R * c; // Distance in km
			return d;
		}

		function deg2rad(deg) {
			return deg * (Math.PI/180);
		}


		return{

			init:function(){
				if(currentUser){
					MessageService.currentUser = currentUser;
					return MessageService.init().then(function(user){
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					});
					
				}else if(UserService.currentUser){
					currentUser = UserService.currentUser;
					MessageService.currentUser = currentUser;
					return MessageService.init().then(function(user){
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					});

				}else{
					return currentUser = UserService.getMe().then(function(user){
						currentUser = user;
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					})
				}

			},

			get:function(annonceId){
				var postData = {id:annonceId, type:'Demenagement', user:currentUser, _format:'json'};
				return $http.post("/api/annonce/get", postData).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},


			getDevis:function(devisId){
				var postData = {id:devisId, type:'Demenagement', user:currentUser, _format:'json'};
				return $http.post("/api/annonce/getDevis", postData).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

			find:function(recherche){
				recherche.type = "Demenagement";
				return $http.post("/api/recherche", recherche).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

			sendMessage:function(message, annonce){
				annonce.type = "Demenagement";
				return MessageService.addFromAnnonce(message, annonce);
			},

			getMessages:function(annonce){
				annonce.type = "Demenagement";
				return MessageService.getFromAnnonce(annonce);
			},

			removeMessage:function(annonce, message, messageId){
				annonce.type = "Demenagement";
				return MessageService.delFromAnnonce(annonce, message, messageId);
			},

			isDispo:function(annonceId, disponnible){
				var postData = {id:annonceId, type:'Demenagement', user:currentUser, disponnible:disponnible, _format:'json'};
				return $http.post("/api/annonce/disponnible", postData).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

		}

	}])

	.service('ReservationService', ['$q', '$http', 'UserService', 'MessageService' , function($q, $http, UserService, MessageService){

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


		///// Calcule Distance

		function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
			var R = 6371; // Radius of the earth in km
			var dLat = deg2rad(lat2-lat1);  // deg2rad below
			var dLon = deg2rad(lon2-lon1); 
			var a = 
			  Math.sin(dLat/2) * Math.sin(dLat/2) +
			  Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
			  Math.sin(dLon/2) * Math.sin(dLon/2)
			  ; 
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
			var d = R * c; // Distance in km
			return d;
		}

		function deg2rad(deg) {
			return deg * (Math.PI/180);
		}


		return{

			init:function(){
				if(currentUser){
					MessageService.currentUser = currentUser;
					return MessageService.init().then(function(user){
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					});
					
				}else if(UserService.currentUser){
					currentUser = UserService.currentUser;
					MessageService.currentUser = currentUser;
					return MessageService.init().then(function(user){
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					});

				}else{
					return currentUser = UserService.getMe().then(function(user){
						currentUser = user;
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					})
				}

			},

			get:function(reservationId){
				var postData = {reservationId:reservationId, type:'Reservation', user:currentUser, _format:'json'};
				return $http.post("/api/reservation/get", postData).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},


			valider:function(reservationId, value){
				var postData = {reservationId:reservationId, valider:value, type:'Reservation', user:currentUser, _format:'json'};
				return $http.post("/api/reservation/valider", postData).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},

			sendMessage:function(message, annonce){
				annonce.type = "Reservation";
				return MessageService.addFromAnnonce(message, annonce);
			},

			getMessages:function(annonce){
				annonce.type = "Reservation";
				return MessageService.getFromAnnonce(annonce);
			},

			removeMessage:function(annonce, message, messageId){
				annonce.type = "Taxi";
				return MessageService.delFromAnnonce(annonce, message, messageId);
			}

		}

	}])


	.service('DevisService', ['$q', '$http', 'UserService', 'MessageService' , function($q, $http, UserService, MessageService){

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


		///// Calcule Distance

		function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
			var R = 6371; // Radius of the earth in km
			var dLat = deg2rad(lat2-lat1);  // deg2rad below
			var dLon = deg2rad(lon2-lon1); 
			var a = 
			  Math.sin(dLat/2) * Math.sin(dLat/2) +
			  Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
			  Math.sin(dLon/2) * Math.sin(dLon/2)
			  ; 
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
			var d = R * c; // Distance in km
			return d;
		}

		function deg2rad(deg) {
			return deg * (Math.PI/180);
		}


		return{

			init:function(){
				if(currentUser){
					MessageService.currentUser = currentUser;
					return MessageService.init().then(function(user){
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					});
					
				}else if(UserService.currentUser){
					currentUser = UserService.currentUser;
					MessageService.currentUser = currentUser;
					return MessageService.init().then(function(user){
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					});

				}else{
					return currentUser = UserService.getMe().then(function(user){
						currentUser = user;
						initialized=true;
						return $q.when(currentUser);
					}, function(err){
						return $q.reject(err);
					})
				}

			},

			get:function(devisId){
				var postData = {devisId:devisId, type:'Devis', user:currentUser, _format:'json'};
				return $http.post("/api/devis/get", postData).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},


			accepter:function(devisId, value){
				var postData = {devisId:devisId, accepter:value, type:'Devis', user:currentUser, _format:'json'};
				return $http.post("/api/devis/accepter", postData).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},


			refuser:function(devisId, value){
				var postData = {devisId:devisId, refuser:value, type:'Devis', user:currentUser, _format:'json'};
				return $http.post("/api/devis/refuser", postData).then(function(response){
					return $q.when(response.data);
				}, function(err){
					return $q.reject(err);
				})
			},


			sendMessage:function(message, annonce){
				annonce.type = "Devis";
				return MessageService.addFromAnnonce(message, annonce);
			},

			getMessages:function(annonce){
				annonce.type = "Devis";
				return MessageService.getFromAnnonce(annonce);
			},

			removeMessage:function(annonce, message, messageId){
				annonce.type = "Taxi";
				return MessageService.delFromAnnonce(annonce, message, messageId);
			}

		}

	}]);