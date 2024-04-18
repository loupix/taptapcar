'use strict';

var myApp = angular.module("taptapcar", ['ui.router','ngAnimate','ngResource', 'ngTouch', 'ngGeolocation','uiGmapgoogle-maps',
	'ui.bootstrap', 'ngFileUpload','angular.google.distance','google.places','djds4rce.angular-socialshare',
	'user.services', 'notificatons.services', 'messages.services', 'annonce.services', 'admin.services'])

myApp.config(function($interpolateProvider){

    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
})

.config(['$httpProvider', function($httpProvider) {
	$httpProvider.defaults.headers.common["X-Requested-With"] =  'XMLHttpRequest';
	$httpProvider.defaults.headers.common["Content-Type"] =  'application/json';
}])


.config(function(uiGmapGoogleMapApiProvider) {
    uiGmapGoogleMapApiProvider.configure({
        key: 'AIzaSyBEBW_t3MvbLz1lGyaNL2lmdW3TDwR2Rm8',
        v: '3.20', //defaults to latest 3.X anyhow
        libraries: 'places,weather,geometry,visualization'
    });
});