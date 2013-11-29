var Sort = angular.module('Sort', []);

Sort.config(['$interpolateProvider', function ($interpolateProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
  }]);
  
  

Sort.controller('SortController', function($scope, $http)
{
    function setData($http)
    {
        $http({method: 'GET', url: '/app_dev.php/?'})
        .success(function(data, status, headers, config) {
            $scope.tickets = data;
        })
    }
    
    var tickets = {};
    
    $scope.init = function () {
        setData($http);
    }
    
    $scope.filter_all = function(filters)
    {
        angular.forEach($scope.tickets, function(value, key){
            console.log(value);
        });
    };
    
    $scope.tickets = tickets;
}
);