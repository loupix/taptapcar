'use strict';

myApp.controller("demenagementRechercheCtrl", ['$scope','$http','$location', 'DemenagementService', 'UserService', function($scope, $http, $location, DemenagementService, UserService){
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

  $scope.recherche = {
    enCours:true,
    type:"Demenagement",
    rayon:document.getElementById("annoncebundle_annonce_rayon_radius").value,
    transporteur:null,
    volume:null,
    hauteur:null,
    largeur:null,
    longueur:null,
    budget:null,
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
    type:"Demenagement",
    rayon:document.getElementById("annoncebundle_annonce_rayon_radius").value,
    transporteur:null,
    volume:null,
    hauteur:null,
    largeur:null,
    longueur:null,
    budget:null,
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



  $scope.go = function ( obj ) {
    window.location= obj.Url;
  };


  $scope.uncheck = function (event) {
    if ($scope.lastRecherche.volume == event.target.value){
      $scope.recherche.volume = null;
      $scope.loadRecherche();
    }
    else if ($scope.lastRecherche.hauteur == event.target.value){
      $scope.recherche.hauteur = null;
      $scope.loadRecherche();
    }
    else if ($scope.lastRecherche.largeur == event.target.value){
      $scope.recherche.largeur = null;
      $scope.loadRecherche();
    }
    else if ($scope.lastRecherche.longueur == event.target.value){
      $scope.recherche.longueur = null;
      $scope.loadRecherche();
    }
    else if ($scope.lastRecherche.note == event.target.value){
      $scope.recherche.note = null;
      $scope.loadRecherche();
    }
    else if ($scope.lastRecherche.budget == event.target.value){
      $scope.recherche.budget = null;
      $scope.loadRecherche();
    }
    else if ($scope.lastRecherche.transporteur == event.target.value){
      $scope.recherche.transporteur = null;
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

  $scope.loadRecherche = function(){
    
    $scope.recherche.enCours = true;
    $scope.recherche._erreur = false;
    UserService.getPosition().then(function(position){
      
      $scope.recherche._position = {'latitude':position.coords.latitude, 'longitude':position.coords.longitude};

      DemenagementService.find($scope.recherche).then(function(response){

        $scope.recherche.enCours = false;
        angular.copy($scope.recherche, $scope.lastRecherche);

        $scope.totalItems = parseInt(response.maxResult);
        $scope.annonces = response.annonces;

        for(var i=0;i<$scope.annonces.length;i++){
          var view = Routing.generate('annonce_demenagement_get', { 'id': $scope.annonces[i].Id });
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





}])






























.controller("demenagementAnnonceCtrl", ['$scope','$http', '$location', 'DemenagementService', function($scope, $http, $location, DemenagementService){

  $scope.annonce = {};
  $scope.user = {};

  // Annonce

  $scope.getAnnonce = function(id){
    DemenagementService.init().then(function(user){
      $scope.user = user;
      DemenagementService.get(id).then(function(annonce){
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
    DemenagementService.getMessages($scope.annonce).then(function(rep){
      if(rep.length>0){
        $scope.messages = rep;
        $scope.message.thread = rep[0].Thread.Id;
      }
    }, function(err){
      console.log(err);
    });
  }

  $scope.sendMessage = function(){
    DemenagementService.sendMessage($scope.message, $scope.annonce).then(function(rep){
      $scope.message.thread = rep.Thread.Id;
      $scope.message.text = "";
      $scope.messages.push(rep);
    }, function(err){
      console.log(err);
    });
  };

  $scope.removeMessage = function(messageId){
    DemenagementService.removeMessage($scope.annonce, $scope.message, messageId).then(function(rep){
      $scope.getMessages();
    }, function(err){
      console.log(err);
    });
  }




}])



























.controller("demenagementReservationCtrl", ['$scope', 'DemenagementService', 'UserService', 'GoogleDistanceAPI', function($scope, DemenagementService, UserService, GoogleDistanceAPI){

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
    distanceTxt:0,
    duree:0,
    dureeTxt:0,
    devis:"Par Kilomètres",
    date:null
  };

  $scope.disponnibilite = {
    heureDepart:null,
    dateDepart:null,
    dispo:null,
    _erreur:null
  };


  $scope.isDispo = function(id){
    $scope.disponnibilite._erreur = false;

    DemenagementService.init().then(function(user){
      $scope.user = user;
      DemenagementService.isDispo(id, $scope.disponnibilite).then(function(data){
        $scope.disponnibilite.dispo = data.value;
      }, function(err){
        console.log(err);
        $scope.disponnibilite._erreur = err.status;
      })
    }, function(err){
      console.log(err);
      $scope.disponnibilite._erreur = err.status;
    })

  };


  $scope.getAnnonce = function(id){
    DemenagementService.init().then(function(user){
      $scope.user = user;
      DemenagementService.get(id).then(function(annonce){
        $scope.annonce = annonce;
        $scope.reservation.prix = annonce.Tarif;
        $scope.reservation.typeCout = annonce.Tarification;
        if(!annonce.transporteur){
          $scope.place.arrivee = {formatted_address:annonce.Arrivee.Adresse};
          $scope.place.depart = {formatted_address:annonce.Depart.Adresse};
          $scope.reservation.date = annonce.JoursUnique;
          $scope.calcDistance();
        }
        
      }, function(err){
        console.log(err);
      })
    }, function(err){
      console.log(err);
    })
  };



  $scope.getDevis = function(id){
    DemenagementService.init().then(function(user){
      $scope.user = user;
      DemenagementService.getDevis(id).then(function(data){
        var annonce = data.Annonce;
        $scope.annonce = annonce;
        if(!annonce.transporteur){
          $scope.place.arrivee = {formatted_address:annonce.Arrivee.Adresse};
          $scope.place.depart = {formatted_address:annonce.Depart.Adresse};
          $scope.reservation.date = annonce.JoursUnique;
        }

        $scope.reservation.devis = data.TypeDevis;
        $scope.reservation.prix = data.PrixUnitaire;

        $scope.calcDistance();
        
      }, function(err){
        console.log(err);
      })
    }, function(err){
      console.log(err);
    })
  };




  $scope.getValue = function(){
    if($scope.reservation.devis=="Par Kilomètres")
      return $scope.reservation.distanceTxt;
    else if($scope.reservation.devis=="Par heures")
      return $scope.reservation.dureeTxt;
    else if($scope.reservation.devis=="Prix unique")
      return 0;
    else
      return 0;
  }


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
    if($scope.reservation.prix)
      return $scope.getPremierTotal($scope.reservation.typeCout);
    return 0;
  }


  $scope.getTotalDevis = function(){
    if($scope.reservation.prix){
      if($scope.reservation.devis=="Par Kilomètres")
        return Math.round($scope.reservation.prix * $scope.reservation.distance * 100)/100;
      else if($scope.reservation.devis=="Par heures")
        return Math.round(($scope.reservation.prix * $scope.reservation.duree/3600)*100)/100;
      else if($scope.reservation.devis=="Par places")
        return Math.round($scope.reservation.prix * $scope.reservation.nbPlaces * 100)/100;
      else if($scope.reservation.devis=="Prix unique")
        return $scope.reservation.prix;
      else
        return Math.round($scope.reservation.prix * $scope.reservation.nbPlaces * 100)/100;
    }
    return 0;
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

    if($type=="depart")
      var place = $scope.place.depart;
    else
      var place = $scope.place.arrivee;
    // console.log(place);

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

    $scope.calcDistance();
  };

  $scope.calcDistance = function(){
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
        console.log($scope.reservation);

      }, function(err){
        console.log(err);
      });
    }
  };

}])
































.controller('demenagementAjoutCtrl', ['$scope',function($scope){
	$scope.placeJeSuis = {
		depart:null,
		arrivee:null,
		depot:null,
    rendezVous:null,
    place:null
	};

	$scope.placeJeRecherche = {
		depart:null,
		arrivee:null,
		depot:null,
    rendezVous:null,
    ville:null
	};


	$scope.loadPlaceJeSuis = function($type){
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
			place = $scope.placeJeSuis.depart;
		}else if($type=="arrivee"){
			place = $scope.placeJeSuis.arrivee;
		}else if($type=="depot"){
			place = $scope.placeJeSuis.depot;
		}else if($type=="rendezVous"){
			place = $scope.placeJeSuis.rendezVous;
		}else if($type=="lieu"){
      place = $scope.placeJeSuis.lieu;
    }
		// console.log(place);
		document.getElementById('annoncebundle_demenagement_transporteur_'+$type+'_adresse').value = place.formatted_address;
    document.getElementById('annoncebundle_demenagement_transporteur_'+$type+'_place_id').value = place.place_id;
		document.getElementById('annoncebundle_demenagement_transporteur_'+$type+'_latitude').value = place.geometry.location.lat();
		document.getElementById('annoncebundle_demenagement_transporteur_'+$type+'_longitude').value = place.geometry.location.lng();
		for (var i = 0; i < place.address_components.length; i++) {
			var addressType = place.address_components[i].types[0];
			if (componentForm[addressType]) {
				var val = place.address_components[i][componentForm[addressType]];
				document.getElementById('annoncebundle_demenagement_transporteur_'+$type+'_'+addressType).value = val;
			}
		}
	};



	$scope.loadPlaceJeRecherche = function($type){
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
			place = $scope.placeJeRecherche.depart;
		}else if($type=="arrivee"){
			place = $scope.placeJeRecherche.arrivee;
		}else if($type=="depot"){
			place = $scope.placeJeRecherche.depot;
		}else if($type=="rendezVous"){
			place = $scope.placeJeRecherche.rendezVous;
		}else if($type=="ville"){
      place = $scope.placeJeRecherche.ville;
    }
		// console.log(place);
    document.getElementById('annoncebundle_demenagement_recherche_'+$type+'_adresse').value = place.formatted_address;
    document.getElementById('annoncebundle_demenagement_recherche_'+$type+'_place_id').value = place.place_id;
		document.getElementById('annoncebundle_demenagement_recherche_'+$type+'_latitude').value = place.geometry.location.lat();
		document.getElementById('annoncebundle_demenagement_recherche_'+$type+'_longitude').value = place.geometry.location.lng();
		for (var i = 0; i < place.address_components.length; i++) {
			var addressType = place.address_components[i].types[0];
			if (componentForm[addressType]) {
				var val = place.address_components[i][componentForm[addressType]];
				document.getElementById('annoncebundle_demenagement_recherche_'+$type+'_'+addressType).value = val;
			}
		}
	};

}])














.controller('demenagementModifCtrl', ['$scope','DemenagementService',function($scope, DemenagementService){

  $scope.placeJeSuis = {
    depart:null,
    arrivee:null,
    depot:null,
    rendezVous:null,
    lieu:null
  };

  $scope.placeJeRecherche = {
    depart:null,
    arrivee:null,
    depot:null,
    rendezVous:null,
    ville:null
  };

  $scope.init = function(id){
    DemenagementService.init().then(function(user){
      $scope.user = user;
      DemenagementService.get(id).then(function(annonce){
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
    if(annonce.Transporteur){
      var place = annonce.Lieu;

      $scope.placeJeSuis.lieu = {
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
      $scope.placeJeRecherche.depart = {
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
      $scope.placeJeRecherche.rendezVous = {
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
      $scope.placeJeRecherche.arrivee = {
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
      $scope.placeJeRecherche.depot = {
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

  $scope.loadPlaceJeSuis = function($type){
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
      place = $scope.placeJeSuis.depart;
    }else if($type=="arrivee"){
      place = $scope.placeJeSuis.arrivee;
    }else if($type=="depot"){
      place = $scope.placeJeSuis.depot;
    }else if($type=="rendezVous"){
      place = $scope.placeJeSuis.rendezVous;
    }else if($type=="lieu"){
      place = $scope.placeJeSuis.lieu;
    }
    // console.log(place);
    document.getElementById('annoncebundle_demenagement_transporteur_'+$type+'_adresse').value = place.formatted_address;
    document.getElementById('annoncebundle_demenagement_transporteur_'+$type+'_place_id').value = place.place_id;
    document.getElementById('annoncebundle_demenagement_transporteur_'+$type+'_latitude').value = place.geometry.location.lat();
    document.getElementById('annoncebundle_demenagement_transporteur_'+$type+'_longitude').value = place.geometry.location.lng();
    for (var i = 0; i < place.address_components.length; i++) {
      var addressType = place.address_components[i].types[0];
      if (componentForm[addressType]) {
        var val = place.address_components[i][componentForm[addressType]];
        document.getElementById('annoncebundle_demenagement_transporteur_'+$type+'_'+addressType).value = val;
      }
    }
  };



  $scope.loadPlaceJeRecherche = function($type){
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
      place = $scope.placeJeRecherche.depart;
    }else if($type=="arrivee"){
      place = $scope.placeJeRecherche.arrivee;
    }else if($type=="depot"){
      place = $scope.placeJeRecherche.depot;
    }else if($type=="rendezVous"){
      place = $scope.placeJeRecherche.rendezVous;
    }else if($type=="ville"){
      place = $scope.placeJeRecherche.ville;
    }
    // console.log(place);
    document.getElementById('annoncebundle_demenagement_recherche_'+$type+'_adresse').value = place.formatted_address;
    document.getElementById('annoncebundle_demenagement_recherche_'+$type+'_place_id').value = place.place_id;
    document.getElementById('annoncebundle_demenagement_recherche_'+$type+'_latitude').value = place.geometry.location.lat();
    document.getElementById('annoncebundle_demenagement_recherche_'+$type+'_longitude').value = place.geometry.location.lng();
    for (var i = 0; i < place.address_components.length; i++) {
      var addressType = place.address_components[i].types[0];
      if (componentForm[addressType]) {
        var val = place.address_components[i][componentForm[addressType]];
        document.getElementById('annoncebundle_demenagement_recherche_'+$type+'_'+addressType).value = val;
      }
    }
  };


}]);
