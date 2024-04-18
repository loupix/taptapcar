'use strict';

myApp.controller("taxiRechercheCtrl", ['$scope','$http','$location', 'TaxiService', 'UserService', function($scope, $http, $location, TaxiService, UserService){
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

  $scope.recherche = {
    enCours:true,
    type:"Taxi",
    rayon:document.getElementById("annoncebundle_annonce_rayon_radius").value,
    wifi:null,
    greencab:null,
    paiement:null,
    siegeBebe:null,
    secuSocial:null,
    animaux:null,
    facture:null,
    prix:null,
    note:null,
    _position:null,
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
    type:"Taxi",
    rayon:document.getElementById("annoncebundle_annonce_rayon_radius").value,
    wifi:null,
    greencab:null,
    paiement:null,
    siegeBebe:null,
    secuSocial:null,
    animaux:null,
    facture:null,
    prix:null,
    note:null,
    _position:null,
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


  $scope.uncheck = function($event){
    if (String($scope.lastRecherche.wifi) == event.target.value){
      $scope.recherche.wifi = null;
      $scope.loadRecherche();
    }
    else if (String($scope.lastRecherche.greencab) == event.target.value){
      $scope.recherche.greencab = null;
      $scope.loadRecherche();
    }
    else if (String($scope.lastRecherche.paiement) == event.target.value){
      $scope.recherche.paiement = null;
      $scope.loadRecherche();
    }
    else if (String($scope.lastRecherche.siegeBebe) == event.target.value){
      $scope.recherche.siegeBebe = null;
      $scope.loadRecherche();
    }
    else if (String($scope.lastRecherche.secuSocial) == event.target.value){
      $scope.recherche.secuSocial = null;
      $scope.loadRecherche();
    }
    else if (String($scope.lastRecherche.animaux) == event.target.value){
      $scope.recherche.animaux = null;
      $scope.loadRecherche();
    }
    else if (String($scope.lastRecherche.facture) == event.target.value){
      $scope.recherche.facture = null;
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


  $scope.go = function ( obj ) {
    window.location= obj.Url;
  };

  $scope.loadRecherche = function(){
    $scope.recherche.enCours = true;
    $scope.recherche._erreur = false;
    UserService.getPosition().then(function(position){

      $scope.recherche._position = {'latitude':position.coords.latitude, 'longitude':position.coords.longitude};

      TaxiService.find($scope.recherche).then(function(response){

        angular.copy($scope.recherche, $scope.lastRecherche);

        $scope.recherche.enCours = false;
        $scope.totalItems = parseInt(response.maxResult);
        $scope.annonces = response.annonces;

        var pos = new Parse.GeoPoint(response.position);

        for(var i=0;i<$scope.annonces.length;i++){
          var view = Routing.generate('annonce_taxi_get', { 'id': $scope.annonces[i].Id });
          $scope.annonces[i].Url = view;

          var profileView = Routing.generate('profile_get', { 'id': $scope.annonces[i].Annonce.Auteur.Id });
          $scope.annonces[i].ProfileUrl = profileView;


          var posAnnonce = new Parse.GeoPoint({latitude:$scope.annonces[i].Ville.Latitude, longitude:$scope.annonces[i].Ville.Longitude});
          $scope.annonces[i].Distance = Math.round(posAnnonce.kilometersTo(pos)*100)/100;
        }

        // filtre annonce
        $scope.annonces.sort(function(a,b){ return a.Distance > b.Distance;});
        


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






















.controller("taxiAnnonceCtrl", ['$scope','$http', '$location', 'TaxiService', function($scope, $http, $location, TaxiService){

  $scope.annonce = {};
  $scope.user = {};

  // Annonce

  $scope.getAnnonce = function(id){
    TaxiService.init().then(function(user){
      $scope.user = user;
      TaxiService.get(id).then(function(annonce){
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
    TaxiService.getMessages($scope.annonce).then(function(rep){
      if(rep.length>0){
        $scope.messages = rep;
        $scope.message.thread = rep[0].Thread.Id;
      }
    }, function(err){
      console.log(err);
    });
  }

  $scope.sendMessage = function(){
    TaxiService.sendMessage($scope.message, $scope.annonce).then(function(rep){
      $scope.message.thread = rep.Thread.Id;
      $scope.message.text = "";
      $scope.messages.push(rep);
    }, function(err){
      console.log(err);
    });
  };

  $scope.removeMessage = function(messageId){
    TaxiService.removeMessage($scope.annonce, $scope.message, messageId).then(function(rep){
      $scope.getMessages();
    }, function(err){
      console.log(err);
    });
  }




}])









.controller("taxiReservationCtrl", ['$scope', 'TaxiService', 'UserService', 'GoogleDistanceAPI', function($scope, TaxiService, UserService, GoogleDistanceAPI){


  $scope.user = {};
  $scope.annonce = {};
  $scope.place = {
    depart:null,
    arrivee:null
  };

  $scope.reservation = {
    prix:0,
    nbPlaces:0,
    distance:0,
    duree:0,
    dureeTxt:0,
    typeCout:"",
    date:null
  };

  $scope.disponnibilite = {
    heureDepart:null,
    dateDepart:null,
    dispo:null,
    _erreur:null
  }


  $scope.isDispo = function(id){
    $scope.disponnibilite._erreur = false;

    TaxiService.init().then(function(user){
      $scope.user = user;
      TaxiService.isDispo(id, $scope.disponnibilite).then(function(data){
        $scope.disponnibilite.dispo = data.value;
        $scope.reservation.prix = data.isDay ? $scope.annonce.TarifJour : $scope.annonce.TarifNuit;
      }, function(err){
        console.log(err);
        $scope.disponnibilite._erreur = err.status;
      })
    }, function(err){
      console.log(err);
      $scope.disponnibilite._erreur = err.status;
    })

  }

  $scope.getAnnonce = function(id){
    TaxiService.init().then(function(user){
      $scope.user = user;
      TaxiService.get(id).then(function(annonce){
        $scope.annonce = annonce;
        $scope.reservation.prix = annonce.IsDay ? annonce.TarifJour : annonce.TarifNuit;
        $scope.reservation.typeCout = annonce.TypeTarif;
      }, function(err){
        console.log(err);
      })
    }, function(err){
      console.log(err);
    })
  };


  $scope.getPremierTotal = function($type){
    if($type=="Par kilomètres")
      return Math.round($scope.reservation.prix * $scope.reservation.distance * 100)/100;
    else if($type=="Par heures")
      return Math.round(($scope.reservation.prix * $scope.reservation.duree/3600)*100)/100;
    else if($type=="Par places")
      return Math.round($scope.reservation.prix * $scope.reservation.nbPlaces * 100)/100;
    else if($type=="Prix unique")
      return $scope.reservation.prix;
    else
      return Math.round($scope.reservation.prix * $scope.reservation.nbPlaces * 100)/100;
  };


  $scope.getTotal = function(){
    return $scope.getPremierTotal($scope.reservation.typeCout);
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

    if($type=="depart")
      var place = $scope.place.depart;
    else
      var place = $scope.place.arrivee;

    document.getElementById($type+'_adresse').value = place.formatted_address;
    document.getElementById($type+'_latitude').value = place.geometry.location.lat();
    document.getElementById($type+'_longitude').value = place.geometry.location.lng();
    document.getElementById($type+'_place_id').value = place.place_id;
    for (var i = 0; i < place.address_components.length; i++) {
      var addressType = place.address_components[i].types[0];
      if (componentForm[addressType]) {
        var val = place.address_components[i][componentForm[addressType]];
        document.getElementById($type+'_'+addressType).value = val;
      }
    }

    if($scope.place.depart!=null && $scope.place.arrivee!=null){
      var args = {
          origins:[$scope.place.depart.formatted_address],
          destinations:[$scope.place.arrivee.formatted_address]
        };

      GoogleDistanceAPI.getDistanceMatrix(args).then(function(distanceMatrix){
        var elt = distanceMatrix.rows[0].elements[0];
        $scope.reservation.distance = Math.round(elt.distance.value/1000, -2);
        $scope.reservation.distanceTxt = elt.distance.text;
        $scope.reservation.duree = elt.duration.value;
        $scope.reservation.dureeTxt = elt.duration.text;

      }, function(err){
        console.log(err);
      });
    }
  };

}])


.controller('taxiAjoutCtrl', ['$scope',function($scope){

	$scope.place = null;


	$scope.loadPlace = function($type){
		var componentForm = {
		    street_number: 'short_name',
		    route: 'long_name',
		    locality: 'long_name',
		    administrative_area_level_1: 'short_name',
		    country: 'long_name',
		    postal_code: 'short_name'
		};

		var place = $scope.place;
		// console.log(place);
		document.getElementById('annoncebundle_taxi_'+$type+'_latitude').value = place.geometry.location.lat();
		document.getElementById('annoncebundle_taxi_'+$type+'_longitude').value = place.geometry.location.lng();
		for (var i = 0; i < place.address_components.length; i++) {
			var addressType = place.address_components[i].types[0];
			if (componentForm[addressType]) {
				var val = place.address_components[i][componentForm[addressType]];
				document.getElementById('annoncebundle_taxi_'+$type+'_'+addressType).value = val;
			}
		}
	};


}])







.controller('taxiModifCtrl', ['$scope','TaxiService',function($scope, TaxiService){

  $scope.place = null;
  $scope.user = null;

  $scope.init = function(id){
    TaxiService.init().then(function(user){
      $scope.user = user;
      TaxiService.get(id).then(function(annonce){
        $scope.annonce = annonce;
        $scope.initPlace(annonce.Ville)
      }, function(err){
        console.log(err);
      })
    }, function(err){
      console.log(err);
    })
  };

  $scope.initPlace=function(place){
    $scope.place = {
      formatted_address:place.Nom,
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
  }

  $scope.loadPlace = function($type){
    var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
    };

    var place = $scope.place;
    // console.log(place);
    document.getElementById('annoncebundle_taxi_'+$type+'_latitude').value = place.geometry.location.lat();
    document.getElementById('annoncebundle_taxi_'+$type+'_longitude').value = place.geometry.location.lng();
    for (var i = 0; i < place.address_components.length; i++) {
      var addressType = place.address_components[i].types[0];
      if (componentForm[addressType]) {
        var val = place.address_components[i][componentForm[addressType]];
        document.getElementById('annoncebundle_taxi_'+$type+'_'+addressType).value = val;
      }
    }
  };


}]);
