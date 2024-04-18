'use strict';

myApp.controller("covoiturageRechercheCtrl", ['$scope','$http','$location', 'CovoiturageService', function($scope, $http, $location, CovoiturageService){
    // Pagination
  $scope.totalItems = 0;
  $scope.currentPage = 0;
  $scope.maxSize = 5;

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
    $scope.recherche._page = $scope.currentPage-1;
    $scope.loadRecherche();
  };



  // Recherche
  $scope.annonces = [];
  $scope.user = {};

  $scope.place = {
    depart:document.getElementById("annoncebundle_annonce_place_from_nom").value,
    arrivee:document.getElementById("annoncebundle_annonce_place_to_nom").value
  };


  console.log($scope.place);

  $scope.recherche = {
    enCours:true,
    type:"Covoiturage",
    depart:[document.getElementById("annoncebundle_annonce_place_from_latitude").value, document.getElementById("annoncebundle_annonce_place_from_longitude").value],
    arrivee:[document.getElementById("annoncebundle_annonce_place_to_latitude").value, document.getElementById("annoncebundle_annonce_place_to_longitude").value],
    date:document.getElementById("annoncebundle_annonce_place_date").value,
    bagages:null,
    preferences:null,
    prix:null,
    note:null,
    prix:null,
    _page:$scope.currentPage,
    _maxSize:$scope.maxSize,
    _format:'json',
    _user:null,
    _erreur:false,
    _tri:{
      heure:null,
      prix:null
    }
  };


  $scope.lastRecherche = {
    enCours:true,
    type:"Covoiturage",
    depart:[document.getElementById("annoncebundle_annonce_place_from_latitude").value, document.getElementById("annoncebundle_annonce_place_from_longitude").value],
    arrivee:[document.getElementById("annoncebundle_annonce_place_to_latitude").value, document.getElementById("annoncebundle_annonce_place_to_longitude").value],
    date:document.getElementById("annoncebundle_annonce_place_date").value,
    bagages:null,
    preferences:null,
    prix:null,
    note:null,
    prix:null,
    _page:$scope.currentPage,
    _maxSize:$scope.maxSize,
    _format:'json',
    _user:null,
    _erreur:false,
    _tri:{
      heure:null,
      prix:null
    }
  };


  $scope.uncheck = function(event){
0
    if ($scope.lastRecherche.bagages == event.target.value){
      $scope.recherche.bagages = null;
      $scope.loadRecherche();
    }
    else if ($scope.lastRecherche.preferences == event.target.value){
      $scope.recherche.preferences = null;
      $scope.loadRecherche();
    }
    else if ($scope.lastRecherche.prix == event.target.value){
      $scope.recherche.prix = null;
      $scope.loadRecherche();
    }
    else if ($scope.lastRecherche.note == event.target.value){
      $scope.recherche.note = null;
      $scope.loadRecherche();
    }
    else if ($scope.lastRecherche._tri.heure == event.target.value){
      $scope.recherche._tri.heure = null;
      $scope.loadRecherche();
    }
    else if ($scope.lastRecherche._tri.prix == event.target.value){
      $scope.recherche._tri.prix = null;
      $scope.loadRecherche();
    }
  }


  $scope.changeGeo = function(){
    $scope.recherche.depard = [document.getElementById("annoncebundle_annonce_place_from_latitude").value, document.getElementById("annoncebundle_annonce_place_from_longitude").value];
    $scope.recherche.arrivee = [document.getElementById("annoncebundle_annonce_place_to_latitude").value, document.getElementById("annoncebundle_annonce_place_to_longitude").value];
    // $scope.recherche.date = document.getElementById("annoncebundle_annonce_place_date").value;
    // console.log($scope.recherche);
  };



  $scope.loadPlace = function($type){
    var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
    };

    var place = null;
    if($type=="from"){
      place = $scope.place.depart;
    }else if($type=="to"){
      place = $scope.place.arrivee;
    }
    // console.log(place);
    document.getElementById('annoncebundle_annonce_place_'+$type+'_nom').value = place.formatted_address;
    document.getElementById('annoncebundle_annonce_place_'+$type+'_latitude').value = place.geometry.location.lat();
    document.getElementById('annoncebundle_annonce_place_'+$type+'_longitude').value = place.geometry.location.lng();
    for (var i = 0; i < place.address_components.length; i++) {
      var addressType = place.address_components[i].types[0];
      if (componentForm[addressType]) {
        var val = place.address_components[i][componentForm[addressType]];
        document.getElementById('annoncebundle_annonce_place_'+$type+'_'+addressType).value = val;
      }
    };

    $scope.changeGeo();
  };




  $scope.go = function ( obj ) {
    window.location= obj.Url;
  };

  $scope.loadRecherche = function(){
    $scope.recherche.enCours = true;
    $scope.recherche._erreur = false;
    CovoiturageService.init().then(function(user){

      $scope.recherche._user = user;

      CovoiturageService.find($scope.recherche).then(function(response){

        angular.copy($scope.recherche, $scope.lastRecherche);
        $scope.recherche.enCours = false;
        $scope.totalItems = parseInt(response.maxResult);
        $scope.annonces = response.annonces;

        for(var i=0;i<$scope.annonces.length;i++){
          var view = Routing.generate('annonce_covoiturage_get', { 'id': $scope.annonces[i].Id });
          $scope.annonces[i].Url = view;

          var profileView = Routing.generate('profile_get', { 'id': $scope.annonces[i].Annonce.Auteur.Id });
          $scope.annonces[i].ProfileUrl = profileView;

          
        }
        


      }, function(err){
        console.log(err);
        $scope.recherche.enCours = false;
        $scope.recherche._erreur = err.status;
      });

    }, function(err){
      console.log(err);
      $scope.recherche.enCours = false;
      $scope.recherche._erreur = err.status;
    });

  };



  $scope.calculeDistance = function(){
    var dep = new Parse.GeoPoint($scope.recherche.depart);
    var arr = new Parse.GeoPoint($scope.recherche.arrivee);
    return Math.round(dep.kilometersTo(arr)*100)/100;
  }


  $scope.calculeDuree = function(){
    return "1h35";
  }




}])





























.controller("covoiturageAnnonceCtrl", ['$scope','$http', '$location', 'CovoiturageService', 'MessageService', function($scope, $http, $location, CovoiturageService){

  $scope.annonce = {};
  $scope.user = {};

  // Annonce

  $scope.getAnnonce = function(id){
    CovoiturageService.init().then(function(user){
      $scope.user = user;
      CovoiturageService.get(id).then(function(annonce){
        $scope.annonce = annonce;
        $scope.getMessages();
      }, function(err){
        console.log(err);
      })
    }, function(err){
      console.log(err);
    })
  };



  // Message

  $scope.messages = [];

  $scope.message = {
    thread:null,
    text:""
  };

  $scope.getMessages = function(){
    CovoiturageService.getMessages($scope.annonce).then(function(rep){
      if(rep.length>0){
        $scope.messages = rep;
        $scope.message.thread = rep[0].Thread.Id;
      }
    }, function(err){
      console.log(err);
    });
  }

  $scope.sendMessage = function(){
    CovoiturageService.sendMessage($scope.message, $scope.annonce).then(function(rep){
      $scope.message.thread = rep.Thread.Id;
      $scope.message.text = "";
      $scope.messages.push(rep);
    }, function(err){
      console.log(err);
    });
  };

  $scope.removeMessage = function(messageId){
    CovoiturageService.removeMessage($scope.annonce, $scope.message, messageId).then(function(rep){
      $scope.getMessages();
    }, function(err){
      console.log(err);
    });
  }




}])

























.controller("covoiturageReservationCtrl", ['$scope', 'CovoiturageService', 'UserService', function($scope, CovoiturageService, UserService){

  $scope.user = {};
  $scope.annonce = {};
  $scope.reservation = {
    prix:0,
    nbPlaces:0,
    date:null
  };

  $scope.getAnnonce = function(id){
    CovoiturageService.init().then(function(user){
      $scope.user = user;
      CovoiturageService.get(id).then(function(annonce){
        $scope.annonce = annonce;
        $scope.reservation.prix = annonce.Cout;
      }, function(err){
        console.log(err);
      })
    }, function(err){
      console.log(err);
    })
  };


  $scope.getTotal = function(){
    var total = $scope.reservation.prix * $scope.reservation.nbPlaces;
    return Math.round(total*100)/100;
  }

}])













.controller('covoiturageAjoutCtrl', ['$scope',function($scope) {
	$scope.placeRegulier = {
		depart:null,
		arrivee:null,
		depot:null,
		rendezVous:null
	};

	$scope.placeUnique = {
		depart:null,
		arrivee:null,
		depot:null,
		rendezVous:null
	};


	$scope.loadPlaceRegulier = function($type){
		var componentForm = {
		    street_number: 'short_name',
		    route: 'long_name',
		    locality: 'long_name',
		    administrative_area_level_1: 'short_name',
		    country: 'long_name',
		    postal_code: 'short_name'
		};

		var place = null;
		if($type=="depart"){
			place = $scope.placeRegulier.depart;
		}else if($type=="arrivee"){
			place = $scope.placeRegulier.arrivee;
		}else if($type=="depot"){
			place = $scope.placeRegulier.depot;
		}else if($type=="rendezVous"){
			place = $scope.placeRegulier.rendezVous;
		}
		// console.log(place);
		document.getElementById('annoncebundle_covoiturage_regulier_'+$type+'_adresse').value = place.formatted_address;
    document.getElementById('annoncebundle_covoiturage_regulier_'+$type+'_place_id').value = place.place_id;
		document.getElementById('annoncebundle_covoiturage_regulier_'+$type+'_latitude').value = place.geometry.location.lat();
		document.getElementById('annoncebundle_covoiturage_regulier_'+$type+'_longitude').value = place.geometry.location.lng();
		for (var i = 0; i < place.address_components.length; i++) {
			var addressType = place.address_components[i].types[0];
			if (componentForm[addressType]) {
				var val = place.address_components[i][componentForm[addressType]];
				document.getElementById('annoncebundle_covoiturage_regulier_'+$type+'_'+addressType).value = val;
			}
		}
	};



	$scope.loadPlaceUnique = function($type){
		var componentForm = {
		    street_number: 'short_name',
		    route: 'long_name',
		    locality: 'long_name',
		    administrative_area_level_1: 'short_name',
		    country: 'long_name',
		    postal_code: 'short_name'
		};

		var place = null;
		if($type=="depart"){
			place = $scope.placeUnique.depart;
		}else if($type=="arrivee"){
			place = $scope.placeUnique.arrivee;
		}else if($type=="depot"){
			place = $scope.placeUnique.depot;
		}else if($type=="rendezVous"){
			place = $scope.placeUnique.rendezVous;
		}
		// console.log(place);
    document.getElementById('annoncebundle_covoiturage_unique_'+$type+'_adresse').value = place.formatted_address;
    document.getElementById('annoncebundle_covoiturage_unique_'+$type+'_place_id').value = place.place_id;
		document.getElementById('annoncebundle_covoiturage_unique_'+$type+'_latitude').value = place.geometry.location.lat();
		document.getElementById('annoncebundle_covoiturage_unique_'+$type+'_longitude').value = place.geometry.location.lng();
		for (var i = 0; i < place.address_components.length; i++) {
			var addressType = place.address_components[i].types[0];
			if (componentForm[addressType]) {
				var val = place.address_components[i][componentForm[addressType]];
				document.getElementById('annoncebundle_covoiturage_unique_'+$type+'_'+addressType).value = val;
			}
		}
	};

}])














.controller('covoiturageModifCtrl', ['$scope','CovoiturageService',function($scope, CovoiturageService){

  $scope.placeRegulier = {
    depart:null,
    arrivee:null,
    depot:null,
    rendezVous:null
  };

  $scope.placeUnique = {
    depart:null,
    arrivee:null,
    depot:null,
    rendezVous:null
  };

  $scope.init = function(id){
    CovoiturageService.init().then(function(user){
      $scope.user = user;
      CovoiturageService.get(id).then(function(annonce){
        $scope.annonce = annonce;
        $scope.initPlace(annonce);
      }, function(err){
        console.log(err);
      })
    }, function(err){
      console.log(err);
    })
  };

  $scope.initPlace=function(annonce){
    if(annonce.Regulier){


      var place = annonce.Depart;
      $scope.placeRegulier.depart = {
        formatted_address:place.Adresse,
        address_components: [
          {
            long_name: place.Route,
            short_name : place.Route,
            types: [ 'route' ]
          },
          {
            long_name: place.StreetNumber,
            short_name : place.StreetNumber,
            types: [ 'street_number' ]
          },
          {
            long_name: place.Locality,
            short_name : place.Locality,
            types: [ 'locality' ]
          },
          {
            long_name: place.AdministrativeAreaLevel1,
            short_name : place.AdministrativeAreaLevel1,
            types: [ 'administrative_area_level_1' ]
          },
          {
            long_name: place.Country,
            short_name : place.Country,
            types: [ 'country' ]
          },
          {
            long_name: place.PostalCode,
            short_name : place.PostalCode,
            types: [ 'postal_code' ]
          }
        ],

        geometry: {
            location: {
                lat: function () { return place.Latitude },
                lng: function () { return place.Longitude }
            }
        }
      };



      var place = annonce.RendezVous;
      $scope.placeRegulier.rendezVous = {
        formatted_address:place.Adresse,
        address_components: [
          {
            long_name: place.Route,
            short_name : place.Route,
            types: [ 'route' ]
          },
          {
            long_name: place.StreetNumber,
            short_name : place.StreetNumber,
            types: [ 'street_number' ]
          },
          {
            long_name: place.Locality,
            short_name : place.Locality,
            types: [ 'locality' ]
          },
          {
            long_name: place.AdministrativeAreaLevel1,
            short_name : place.AdministrativeAreaLevel1,
            types: [ 'administrative_area_level_1' ]
          },
          {
            long_name: place.Country,
            short_name : place.Country,
            types: [ 'country' ]
          },
          {
            long_name: place.PostalCode,
            short_name : place.PostalCode,
            types: [ 'postal_code' ]
          }
        ],

        geometry: {
            location: {
                lat: function () { return place.Latitude },
                lng: function () { return place.Longitude }
            }
        }
      };



      var place = annonce.Arrivee;
      $scope.placeRegulier.arrivee = {
        formatted_address:place.Adresse,
        address_components: [
          {
            long_name: place.Route,
            short_name : place.Route,
            types: [ 'route' ]
          },
          {
            long_name: place.StreetNumber,
            short_name : place.StreetNumber,
            types: [ 'street_number' ]
          },
          {
            long_name: place.Locality,
            short_name : place.Locality,
            types: [ 'locality' ]
          },
          {
            long_name: place.AdministrativeAreaLevel1,
            short_name : place.AdministrativeAreaLevel1,
            types: [ 'administrative_area_level_1' ]
          },
          {
            long_name: place.Country,
            short_name : place.Country,
            types: [ 'country' ]
          },
          {
            long_name: place.PostalCode,
            short_name : place.PostalCode,
            types: [ 'postal_code' ]
          }
        ],

        geometry: {
            location: {
                lat: function () { return place.Latitude },
                lng: function () { return place.Longitude }
            }
        }
      };



      var place = annonce.Depot;
      $scope.placeRegulier.depot = {
        formatted_address:place.Adresse,
        address_components: [
          {
            long_name: place.Route,
            short_name : place.Route,
            types: [ 'route' ]
          },
          {
            long_name: place.StreetNumber,
            short_name : place.StreetNumber,
            types: [ 'street_number' ]
          },
          {
            long_name: place.Locality,
            short_name : place.Locality,
            types: [ 'locality' ]
          },
          {
            long_name: place.AdministrativeAreaLevel1,
            short_name : place.AdministrativeAreaLevel1,
            types: [ 'administrative_area_level_1' ]
          },
          {
            long_name: place.Country,
            short_name : place.Country,
            types: [ 'country' ]
          },
          {
            long_name: place.PostalCode,
            short_name : place.PostalCode,
            types: [ 'postal_code' ]
          }
        ],

        geometry: {
            location: {
                lat: function () { return place.Latitude },
                lng: function () { return place.Longitude }
            }
        }
      };

      


    }else{

      var place = annonce.Depart;
      $scope.placeUnique.depart = {
        formatted_address:place.Adresse,
        address_components: [
          {
            long_name: place.Route,
            short_name : place.Route,
            types: [ 'route' ]
          },
          {
            long_name: place.StreetNumber,
            short_name : place.StreetNumber,
            types: [ 'street_number' ]
          },
          {
            long_name: place.Locality,
            short_name : place.Locality,
            types: [ 'locality' ]
          },
          {
            long_name: place.AdministrativeAreaLevel1,
            short_name : place.AdministrativeAreaLevel1,
            types: [ 'administrative_area_level_1' ]
          },
          {
            long_name: place.Country,
            short_name : place.Country,
            types: [ 'country' ]
          },
          {
            long_name: place.PostalCode,
            short_name : place.PostalCode,
            types: [ 'postal_code' ]
          }
        ],

        geometry: {
            location: {
                lat: function () { return place.Latitude },
                lng: function () { return place.Longitude }
            }
        }
      };



      var place = annonce.RendezVous;
      $scope.placeUnique.rendezVous = {
        formatted_address:place.Adresse,
        address_components: [
          {
            long_name: place.Route,
            short_name : place.Route,
            types: [ 'route' ]
          },
          {
            long_name: place.StreetNumber,
            short_name : place.StreetNumber,
            types: [ 'street_number' ]
          },
          {
            long_name: place.Locality,
            short_name : place.Locality,
            types: [ 'locality' ]
          },
          {
            long_name: place.AdministrativeAreaLevel1,
            short_name : place.AdministrativeAreaLevel1,
            types: [ 'administrative_area_level_1' ]
          },
          {
            long_name: place.Country,
            short_name : place.Country,
            types: [ 'country' ]
          },
          {
            long_name: place.PostalCode,
            short_name : place.PostalCode,
            types: [ 'postal_code' ]
          }
        ],

        geometry: {
            location: {
                lat: function () { return place.Latitude },
                lng: function () { return place.Longitude }
            }
        }
      };



      var place = annonce.Arrivee;
      $scope.placeUnique.arrivee = {
        formatted_address:place.Adresse,
        address_components: [
          {
            long_name: place.Route,
            short_name : place.Route,
            types: [ 'route' ]
          },
          {
            long_name: place.StreetNumber,
            short_name : place.StreetNumber,
            types: [ 'street_number' ]
          },
          {
            long_name: place.Locality,
            short_name : place.Locality,
            types: [ 'locality' ]
          },
          {
            long_name: place.AdministrativeAreaLevel1,
            short_name : place.AdministrativeAreaLevel1,
            types: [ 'administrative_area_level_1' ]
          },
          {
            long_name: place.Country,
            short_name : place.Country,
            types: [ 'country' ]
          },
          {
            long_name: place.PostalCode,
            short_name : place.PostalCode,
            types: [ 'postal_code' ]
          }
        ],

        geometry: {
            location: {
                lat: function () { return place.Latitude },
                lng: function () { return place.Longitude }
            }
        }
      };



      var place = annonce.Depot;
      $scope.placeUnique.depot = {
        formatted_address:place.Adresse,
        address_components: [
          {
            long_name: place.Route,
            short_name : place.Route,
            types: [ 'route' ]
          },
          {
            long_name: place.StreetNumber,
            short_name : place.StreetNumber,
            types: [ 'street_number' ]
          },
          {
            long_name: place.Locality,
            short_name : place.Locality,
            types: [ 'locality' ]
          },
          {
            long_name: place.AdministrativeAreaLevel1,
            short_name : place.AdministrativeAreaLevel1,
            types: [ 'administrative_area_level_1' ]
          },
          {
            long_name: place.Country,
            short_name : place.Country,
            types: [ 'country' ]
          },
          {
            long_name: place.PostalCode,
            short_name : place.PostalCode,
            types: [ 'postal_code' ]
          }
        ],

        geometry: {
            location: {
                lat: function () { return place.Latitude },
                lng: function () { return place.Longitude }
            }
        }
      };
    };
  };

  $scope.loadPlaceRegulier = function($type){
    var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
    };

    var place = null;
    if($type=="depart"){
      place = $scope.placeRegulier.depart;
    }else if($type=="arrivee"){
      place = $scope.placeRegulier.arrivee;
    }else if($type=="depot"){
      place = $scope.placeRegulier.depot;
    }else if($type=="rendezVous"){
      place = $scope.placeRegulier.rendezVous;
    }
    // console.log(place);
    document.getElementById('annoncebundle_covoiturage_regulier_'+$type+'_adresse').value = place.formatted_address;
    document.getElementById('annoncebundle_covoiturage_regulier_'+$type+'_place_id').value = place.place_id;
    document.getElementById('annoncebundle_covoiturage_regulier_'+$type+'_latitude').value = place.geometry.location.lat();
    document.getElementById('annoncebundle_covoiturage_regulier_'+$type+'_longitude').value = place.geometry.location.lng();
    for (var i = 0; i < place.address_components.length; i++) {
      var addressType = place.address_components[i].types[0];
      if (componentForm[addressType]) {
        var val = place.address_components[i][componentForm[addressType]];
        document.getElementById('annoncebundle_covoiturage_regulier_'+$type+'_'+addressType).value = val;
      }
    }
  };



  $scope.loadPlaceUnique = function($type){
    var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
    };

    var place = null;
    if($type=="depart"){
      place = $scope.placeUnique.depart;
    }else if($type=="arrivee"){
      place = $scope.placeUnique.arrivee;
    }else if($type=="depot"){
      place = $scope.placeUnique.depot;
    }else if($type=="rendezVous"){
      place = $scope.placeUnique.rendezVous;
    }
    // console.log(place);
    document.getElementById('annoncebundle_covoiturage_unique_'+$type+'_adresse').value = place.formatted_address;
    document.getElementById('annoncebundle_covoiturage_unique_'+$type+'_place_id').value = place.place_id;
    document.getElementById('annoncebundle_covoiturage_unique_'+$type+'_latitude').value = place.geometry.location.lat();
    document.getElementById('annoncebundle_covoiturage_unique_'+$type+'_longitude').value = place.geometry.location.lng();
    for (var i = 0; i < place.address_components.length; i++) {
      var addressType = place.address_components[i].types[0];
      if (componentForm[addressType]) {
        var val = place.address_components[i][componentForm[addressType]];
        document.getElementById('annoncebundle_covoiturage_unique_'+$type+'_'+addressType).value = val;
      }
    }
  };


}]);