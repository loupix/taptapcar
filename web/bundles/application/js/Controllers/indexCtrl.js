'use strict';

myApp.controller("rechercheCtrl", ['$scope', function($scope){


	$scope.autocompleteOptions = {
        componentRestrictions: { country: 'fr' },
        types: ['geocode']
  };


	$scope.place = {
		depard:null,
		arrivee:null,
		date:null,
		rayon:null
	};

	$scope.categorie = {
		demenagement:true,
		covoiturage:false,
		vtc:false,
		taxi:false
	};


  $scope.submitForm = function(obj){
    document.getElementById("formSearch").submit();
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

		var place = null;
		if($type=="from"){
			place = $scope.place.depard;
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
		}
	};


	

	$scope.changeCategorie = function($type){
		if($type=="vtc"){
			$scope.categorie = {
				demenagement:false,
				covoiturage:false,
				vtc:true,
				taxi:false
			}
		}else if($type=="taxi"){
			$scope.categorie = {
				demenagement:false,
				covoiturage:false,
				vtc:false,
				taxi:true
			}
		}else if($type=="demenagement"){
			$scope.categorie = {
				demenagement:true,
				covoiturage:false,
				vtc:false,
				taxi:false
			}
		}else if($type=="covoiturage"){
			$scope.categorie = {
				demenagement:false,
				covoiturage:true,
				vtc:false,
				taxi:false
			}
		};

		document.getElementById('annoncebundle_annonce_categorie_0').checked = $scope.categorie.demenagement;
		document.getElementById('annoncebundle_annonce_categorie_1').checked = $scope.categorie.vtc;
		document.getElementById('annoncebundle_annonce_categorie_2').checked = $scope.categorie.taxi;
		document.getElementById('annoncebundle_annonce_categorie_3').checked = $scope.categorie.covoiturage;

		// console.log($scope.categorie);

	}

	$scope.submitform = function(){
		console.log("posting data....");
	}
}])


.controller('DatepickerPopupCtrl', function ($scope) {
  $scope.today = function() {
    $scope.dt = new Date();
  };
  $scope.today();

  $scope.clear = function() {
    $scope.dt = null;
  };

  $scope.inlineOptions = {
    customClass: getDayClass,
    minDate: new Date(),
    showWeeks: true
  };

  $scope.dateOptions = {
    dateDisabled: disabled,
    formatYear: 'yy',
    maxDate: new Date(2020, 5, 22),
    minDate: new Date(),
    startingDay: 1
  };

  // Disable weekend selection
  function disabled(data) {
    var date = data.date,
      mode = data.mode;
    return mode === 'day' && (date.getDay() === 0 || date.getDay() === 6);
  }

  $scope.toggleMin = function() {
    $scope.inlineOptions.minDate = $scope.inlineOptions.minDate ? null : new Date();
    $scope.dateOptions.minDate = $scope.inlineOptions.minDate;
  };

  $scope.toggleMin();

  $scope.open1 = function() {
    $scope.popup1.opened = true;
  };

  $scope.open2 = function() {
    $scope.popup2.opened = true;
  };

  $scope.setDate = function(year, month, day) {
    $scope.dt = new Date(year, month, day);
  };

  $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate', 'dd/MM/yyyy'];
  $scope.format = $scope.formats[4];
  $scope.altInputFormats = ['M!/d!/yyyy'];

  $scope.popup1 = {
    opened: false
  };

  $scope.popup2 = {
    opened: false
  };

  var tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  var afterTomorrow = new Date();
  afterTomorrow.setDate(tomorrow.getDate() + 1);
  $scope.events = [
    {
      date: tomorrow,
      status: 'full'
    },
    {
      date: afterTomorrow,
      status: 'partially'
    }
  ];

  function getDayClass(data) {
    var date = data.date,
      mode = data.mode;
    if (mode === 'day') {
      var dayToCheck = new Date(date).setHours(0,0,0,0);

      for (var i = 0; i < $scope.events.length; i++) {
        var currentDay = new Date($scope.events[i].date).setHours(0,0,0,0);

        if (dayToCheck === currentDay) {
          return $scope.events[i].status;
        }
      }
    }

    return '';
  }



  $scope.changeDate = function(){
  	document.getElementById("annoncebundle_annonce_place_date").setAttribute("value", $scope.dt.toLocaleDateString());
  }


});