angular.module('app', ['ngMessages', 'initialValue'])
    .controller('FormCtrl', function($scope, $http) {

        $scope.fakeConfirmacion = [
            '00000',
            '12345'
        ];

        /*$scope.fakeConfirmacion = function (task) {
         $http.get("getConfirmUser").success(function(data){
         $scope.fakeConfirmacion = data;
         });
         };*/

    })

    .directive('validFile',function(){
        return {
            require:'ngModel',
            link:function(scope,el,attrs,ngModel){
                //change event is fired when file is selected
                el.bind('change',function(){
                    scope.$apply(function(){
                        ngModel.$setViewValue(el.val());
                        ngModel.$render();
                    })
                })
            }
        }
    })

    .directive('uniqueCode', function($http, $q) {
        return {
            require : 'ngModel',
            link : function($scope, element, attrs, ngModel) {
                ngModel.$asyncValidators.uniqueEmail = function(modelValue, viewValue) {
                    var code = modelValue || viewValue;
                    return $http.post('validCodeCel', {code: code}).then(function(res) {
                        if (res.data == 0) {
                            ngModel.$setValidity('uniqueCode', false);
                            return $q.reject();
                        }
                        ngModel.$setValidity('uniqueCode', true);
                        return $q.when();
                    });
                }; // end async
            } // end link
        } // end return
    })

    .factory('fakeQueryService', function($timeout, $q) {
        var FAKE_TIMEOUT = 2000;
        return function(username, fakeInvalidData) {
            var defer = $q.defer();
            $timeout(function() {
                fakeInvalidData.indexOf(username) == -1
                    ? defer.resolve()
                    : defer.reject();
            }, FAKE_TIMEOUT);
            return defer.promise;
        }
    })

    .directive('fakeRemoteRecordValidator', ['$timeout', 'fakeQueryService', function($timeout, fakeQueryService) {
        return {
            require: 'ngModel',
            link : function(scope, element, attrs, ngModel) {
                var seedData = scope.$eval(attrs.fakeRemoteRecordValidator);
                ngModel.$parsers.push(function(value) {
                    valid(false);
                    loading(true);
                    fakeQueryService(value, seedData).then(
                        function() {
                            valid(true);
                            loading(false);
                        },
                        function() {
                            valid(false);
                            loading(false);
                        }
                    );
                    return value;
                });

                function valid(bool) {
                    ngModel.$setValidity('record-taken', bool);
                }

                function loading(bool) {
                    ngModel.$setValidity('record-loading', !bool);
                }
            }
        }
    }])


//jQuery time
var current_fs,
    next_fs,
    previous_fs;
//fieldsets
var left,
    opacity,
    scale;
//fieldset properties which we will animate
var animating;



function nextStep(step) {
    if(step == 11){
        validCodeCel();
        return;
    }

    if(step == 4) {
        validCredit();
        return;
    }else{
        if(step != 40){
            saveStep(step);
        }
    }

    if(step == 40){
        step = 4;
    }


    if (animating)
        return false;
    animating = true;

    current_fs = $("#step"+step);
    next_fs = $("#step"+(step+1));


    $("#prog"+(step+1)).addClass("active");
    next_fs.show();
    current_fs.animate({
        opacity : 0
    }, {
        step : function(now, mx) {
            scale = 1 - (1 - now) * 0.2;
            left = (now * 50) + "%";
            opacity = 1 - now;
            current_fs.css({
                'transform' : 'scale(' + scale + ')'
            });
            next_fs.css({
                'left' : left,
                'opacity' : opacity
            });
        },
        duration : 800,
        complete : function() {
            current_fs.hide();
            animating = false;
        },
        easing : 'easeInOutBack'
    });

    $('html,body').animate({
     scrollTop: $("#progressbar").offset().top
     }, 2800);
}



function prevStep(step) {
    if (animating)
        return false;
    animating = true;

    current_fs = $("#step"+step);
    previous_fs = $("#step"+(step-1));

    $("#prog"+(step)).removeClass("active");

    previous_fs.show();
    current_fs.animate({
        opacity : 0
    }, {
        step : function(now, mx) {
            scale = 0.8 + (1 - now) * 0.2;
            left = ((1 - now) * 50) + "%";
            opacity = 1 - now;
            current_fs.css({
                'left' : left
            });
            previous_fs.css({
                'transform' : 'scale(' + scale + ')',
                'opacity' : opacity
            });
        },
        duration : 800,
        complete : function() {
            current_fs.hide();
            animating = false;
        },
        easing : 'easeInOutBack'
    });

    $('html,body').animate({
        scrollTop: $("#progressbar").offset().top
    }, 800);
}

function goStep(step){

     for (i=1;i<=5;i++){
        $("#prog"+(i)).removeClass("active");
        $("#step"+i).fadeOut(500);
        //console.log("hideStep-"+i);
    }

    for (i=0;i<=step;i++){
        $("#prog"+(i)).addClass("active");
    }


    $("#step"+step).fadeIn(500);
    //console.log("showStep-"+step);

    $('html,body').animate({
        scrollTop: $("#progressbar").offset().top
    }, 2800);

}

function countCP(cp,type){
    if(cp.length == 5){
        getAddressByCp(cp,type);
    }
}

function getAddressByCp(cp,type){
    $.ajax({
        data:  {cp: cp},
        url: "getDirections",
        type:  'post',
        success:  function (response) {
            if(type == 1){
                $("#colonia").html("<option value='?'></option>"+response);
                $("#colonia").focus();
            }

            if(type == 2){
                $("#coloniaTrabajo").html("<option value='?'></option>"+response);
                $("#coloniaTrabajo").focus();
            }

        }
    });
}

function validCredit(){
    $.ajax({
        data:  {a: 1},
        url: "validCredit",
        type:  'post',
        beforeSend: function () {
            $(".popup-modal-valid").click();
        },
        success:  function (response) {
            //console.log(response);
            if(response == 1){
                $.magnificPopup.close();
                saveStep(4);
                nextStep(40);
            }else{
                $.magnificPopup.close();
                nextStep(40);
                $("#step5cont").html('<h2 style="text-transform: uppercase;">Lo Sentimos<br>Tu crédito no fue aprobado.</h2>');
            }

        }
    });
}

function saveStep(step){
    if(step == 5){
        setTimeout ("loadFrmF();", 800);
    }else{
        $.ajax({
            data:  $("#step"+step).serialize(),
            url: "saveStep"+step,
            type:  'post',
            success:  function (response) {
                //console.log(response);
            }
        });
    }

}



function loadFrmF(){
    var ifrAdd = $("#ifrmUpload").contents().find("#idResp").html();
    /*console.log(ifrAdd);
    if(ifrAdd > 0){
        console.log("se adjuntaron los archivos");
        $(".popup-modal-success").click();
        setTimeout ("window.location.href = '../faqs';", 5000);
    }else{
        console.log("noooo");
        return;
    }*/
    $(".popup-modal-success").click();
    setTimeout ("window.location.href = '../faqs';", 5000);
}


$('.popup-modal-valid').magnificPopup({
    type: 'inline',
    preloader: false,
    focus: '#username',
    modal: true
});

$('.popup-modal-success').magnificPopup({
    type: 'inline',
    preloader: false,
    modal: true
});


$(document).on('click', '.popup-modal-dismiss', function (e) {
    e.preventDefault();
    $.magnificPopup.close();
});


function checkCel(){
    var cel = $("#celular").val().replace("(", "");
    cel =cel.replace(")", "");
    cel = cel.replace(/-/g, "");
    cel = cel.replace(" ", "");

    $.ajax({
        data:  { cel: cel },
        url: "checkCel",
        type:  'post',
        success:  function (response) {
            console.log(response);

        }
    });

}

function validCodeCel(){
    console.log("val code");
    $.ajax({
        data:  { code:  $("#confirmacion").val() },
        url: "validCodeCel",
        type:  'post',
        success:  function (response) {
            //console.log("aaaa: "+response);
            if(response == 1){
                console.log("aaaaI: "+response);
                nextStep(1);
            }else {
                console.log("aaaaE: "+response);
                $("#ShowCodeModal").modal('show');
                return;
            }
        }
    });

}