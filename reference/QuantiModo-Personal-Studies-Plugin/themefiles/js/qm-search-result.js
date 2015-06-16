// Define a new module for our app
var app = angular.module("instantSearch", ['ui.bootstrap']);

// Create the instant search filter

app.filter('searchFor', function() {

    // All filters must return a function. The first parameter
    // is the data that is to be filtered, and the second is an
    // argument that may be passed with a colon (searchFor:searchString)

    return function(arr, searchString) {

        if (!searchString) {
            return arr;
        }

        var result = [];

        searchString = searchString.toLowerCase();

        // Using the forEach helper method to loop through the array
        angular.forEach(arr, function(item) {

            if (item.variable.toLowerCase().indexOf(searchString) !== -1) {
                result.push(item);
            }

        });

        return result;
    };

});

// The controller

function InstantSearchController($scope) {
    $scope.correlations = [];
    $scope.totalCorrelations = [];    
    $scope.maxSize = 10;
    $scope.itemsPerPage = 10;
    
    $scope.initData = function() {
        $scope.totalCorrelations = SearchPage.correlations;
        $scope.bigTotalItems = $scope.totalCorrelations.length;
        $scope.bigCurrentPage = 1;        
    };

    $scope.loadCurrentPageData = function() {
        $scope.correlations = $scope.totalCorrelations.slice(($scope.bigCurrentPage - 1) * $scope.itemsPerPage, $scope.itemsPerPage * $scope.bigCurrentPage);       
    };

    $scope.loadData = function() {
           $scope.initData();
           $scope.loadCurrentPageData();
    };

    $scope.setPage = function(pageNo) {
        $scope.currentPage = pageNo;
    };

    $scope.pageChanged = function() {
        $scope.loadCurrentPageData();
    };
    
    $scope.isNotEmpty = function () {
       return $scope.totalCorrelations.length > 10; 
    };
}
