App = Ember.Application.create();
$.cookie.json = true;

App.TimeFormat = {
    datetimepicker: 'yyyy.mm.dd hh:ii',
    moment: 'YYYY.MM.DD HH:mm'
};

App.Api = {
    call: function(uri, type, parameters, callback, callbackOnError, fromLogin) {
        fromLogin = typeof fromLogin !== 'undefined' ? fromLogin : false;
        var url = 'api/v1/' + uri;

        if (parameters) {
            url += '?' + $.param(parameters);
        }

        App.loading(true);

        $.ajax({
            url: url,
            dataType: 'json',
            type: type
        }).done(function(data, textStatus, jqXHR) {
            if (callback) {
                callback(data.result);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            if (jqXHR.status == '401' && !fromLogin) {
                App.login();

                return;
            }

            if (callbackOnError) {
                callbackOnError(jqXHR);
            }
        }).always(function() {
            App.loading(false);
        });
    }
}

App.setMe = function(me) {
    $.cookie('me', me);
};

App.getMe = function() {
    return $.cookie('me');
}

App.removeMe = function() {
    $.removeCookie('me');
}

App.AlertView = Ember.View.extend({
    templateName: 'alert',
    classNames: ['modal', 'fade'],
    didInsertElement: function() {
        this.$().on('hidden.bs.modal', function() {
            $(this).remove();
        });

        this.$().modal('show');
    }
});

App.alert = function(title, message) {
    /*
        TODO: it throws 'DEPRECATION' error, but still working
        we should fix this to not throw error
    */
    App.AlertView.create({
        context: {
            title: title,
            message: message,
        }
    }).appendTo('body');
}

App.alertWithRequestError = function(error) {
    if (error.status == '400') {
        App.alert('Error', 'Invalid parameters');
    } else if (error.status == '403') {
        App.alert('Error', 'You have no permission to this');
    } else if (error.status == '404') {
        App.alert('Error', 'Couldn\'t find the resource');
    } else {
        App.alert('Error', 'An unknown error occurred');
    }
}

App.ConfirmView = Ember.View.extend({
    templateName: 'confirm',
    classNames: ['modal', 'fade'],
    didInsertElement: function() {
        this.$().on('hidden.bs.modal', function() {
            $(this).remove();
        });

        this.$().modal('show');
    }
});

App.ConfirmController = Ember.ObjectController.extend({
    title: '',
    message: '',
    callback: null,
    parameter: null,
    actions: {
        OK: function(view) {
            view.$().modal('hide');
            this.callback();
        }
    }
});

App.confirm = function(title, message, callback) {
    /*
        TODO: it throws 'DEPRECATION' error, but still working
        we should fix this to not throw error
    */
    App.ConfirmView.create().set('controller', App.ConfirmController.create({
        title: title,
        message: message,
        callback: callback
    })).appendTo('body');
}

App.LoginView = Ember.View.extend({
    templateName: 'login',
    classNames: ['modal', 'fade'],
    didInsertElement: function() {
        this.$().on('hidden.bs.modal', function() {
            $(this).remove();
        });

        this.$().modal({
            backdrop: 'static',
            keyboard: false
        });
    }
});

App.LoginController = Ember.ObjectController.extend({
    username: '',
    password: '',
    errorMessage: '',
    validate: function() {
        if (!this.username) {
            return 'Username is empty';
        }

        if (!this.password) {
            return 'Password is empty';
        }

        return null;
    },
    actions: {
        login: function(view) {
            this.set('errorMessage', this.validate());

            if (this.errorMessage) {
                return;
            }

            App.Api.call('login', 'POST', {
                username: this.username,
                password: this.password
            }, function(result) {
                App.setMe(result);

                view.$().modal('hide');
                location.reload();
            }, function(error) {
                if (error.status == 401) {
                    App.alert('Error', 'Username or password is incorrect');
                } else {
                    App.alert('Error', 'An unknown error occurred');
                }
            }, true);
        }
    }
});

App.login = function() {
    /*
        TODO: it throws 'DEPRECATION' error, but still working
        we should fix this to not throw error
    */
    App.LoginView.create().set('controller', App.LoginController.create()).appendTo('body');
}

App.loading = function(show) {
    if (show) {
        $('#loadingWindow').modal({
            backdrop: 'static',
            keyboard: false
        });
    } else {
        $('#loadingWindow').modal('hide');
    }
}

App.LunchFormView = Ember.View.extend({
    templateName: 'lunchForm',
    classNames: ['modal', 'fade'],
    didInsertElement: function() {
        this.$().on('hidden.bs.modal', function() {
            $(this).remove();

            /*
              TODO: We have to remove also datetimepickers elements which created outside of modal view
              '$(this).remove();' doesn't remove outside elements
             */
        });
        this.$().on('shown.bs.modal', function() {
            $('.form_datetime').datetimepicker({
                format: App.TimeFormat.datetimepicker,
                autoclose: true,
                pickerPosition: 'bottom-left',
                minuteStep: 10
            });
        });

        this.$().modal('show');
    }
});

App.LunchFormController = Ember.ObjectController.extend({
    formTitle: 'Edit',
    theme: '',
    location: '',
    description: '',
    beginTime: 0,
    endTime: 0,
    beginTimeString: '',
    endTimeString: '',
    minPeople: '',
    maxPeople: '',
    errorMessage: '',
    validate: function() {
        if (!this.theme) {
            return 'Theme is empty';
        }

        if (!this.location) {
            return 'Location is empty';
        }

        if (!this.description) {
            return 'Description is empty';
        }

        if (!this.beginTimeString){
            return 'Begin Time is empty';
        }
        
        if (this.beginTime * 1000 < (new Date()).getTime()) {
            return 'Begin Time is past';
        }

        if (!this.endTimeString){
            return 'End Time is empty';
        }

        if (this.endTime < this.beginTime) {
            return 'End Time is older than Begin Time';
        }

        if (!this.minPeople) {
            return 'Min People is empty';
        }

        if (!this.minPeople.match(/^\d{1,2}$/)) {
            return 'Min People has to be from 1 to 99';
        }

        if (!this.maxPeople) {
            return 'Max People is empty';
        }

        if (!this.maxPeople.match(/^\d{1,2}$/)) {
            return 'Max People has to be from 1 to 99';
        }

        if (parseInt(this.minPeople) > parseInt(this.maxPeople)) {
            return 'Min People is larger then Max People';
        }

        return null;
    },
    actions: {
        submit: function(view) {
            this.beginTime = new Date(this.beginTimeString).getTime() / 1000;
            this.endTime = new Date(this.endTimeString).getTime() / 1000;
            this.set('errorMessage', this.validate());

            if (this.errorMessage) {
                return;
            }

            view.$().modal('hide');
            this.callback({
                theme: this.theme,
                location: this.location,
                description: this.description,
                beginTime: this.beginTime,
                endTime: this.endTime,
                minPeople: this.minPeople,
                maxPeople: this.maxPeople
            });
        }
    }
});

App.lunchForm = function(model, callback) {
    var parameters = {callback: callback};

    if (model) {
        parameters = {
            theme: model.theme,
            location: model.location,
            description: model.description,
            beginTime: model.beginTime,
            endTime: model.endTime,
            beginTimeString: moment.unix(model.beginTime).format(App.TimeFormat.moment),
            endTimeString: moment.unix(model.endTime).format(App.TimeFormat.moment),
            minPeople: model.minPeople,
            maxPeople: model.maxPeople,
            callback: callback
        }
    }

    /*
        TODO: it throws 'DEPRECATION' error, but still working
        we should fix this to not throw error
    */
    App.LunchFormView.create().set('controller', App.LunchFormController.create(parameters)).append();
}

App.LunchActions = Ember.Mixin.create({
    actions: {
        edit: function(model) {
            App.lunchForm(model, function(parameters) {
                parameters.lunchId = model.lunchId;

                App.Api.call('lunch', 'POST', parameters, function(result) {
                    // TODO: this is lame
                    location.reload();
                }, function(error) {
                    App.alertWithRequestError(error);
                });
            });
        },
        delete: function(model) {
            App.confirm('Confirmation', 'Do you really want to delete this lunch?', function() {
                App.Api.call('lunch', 'DELETE', {lunchId: model.lunchId}, function(result) {
                    // TODO: this is lame
                    location.reload();
                }, function(error) {
                    App.alertWithRequestError(error);
                });
            });
        },
        join: function(model) {
            App.Api.call('lunch/' + model.lunchId + '/member', 'PUT', null, function(result) {
                // TODO: this is lame
                location.reload();
            }, function(error) {
                App.alertWithRequestError(error);
            });
        },
        cancel: function(model) {
            App.confirm('Confirmation', 'Do you really want to cancel joining this lunch?', function() {
                App.Api.call('lunch/' + model.lunchId + '/member', 'DELETE', null, function(result) {
                    // TODO: this is lame
                    location.reload();
                }, function(error) {
                    App.alertWithRequestError(error);
                });
            });
        },
        addComment: function(model) {
            if (model.commentToAdd.length < 1) {
                return;
            }

            App.Api.call('lunch/' + model.lunchId + '/comment', 'PUT', {content: model.commentToAdd}, function(result) {
                // TODO: this is lame
                location.reload();
            }, function(error) {
                App.alertWithRequestError(error);
            });
        },
        deleteComment: function(model) {
        }
    }
});

App.ApplicationRoute = Ember.Route.extend({
    model: function() {
        return App.getMe();
    },
    actions: {
        createLunch: function() {
            App.lunchForm(null, function(parameters) {
                App.Api.call('lunch', 'PUT', parameters, function(result) {
                    // TODO: this is lame
                    location.reload();
                }, function(error) {
                    App.alertWithRequestError(error);
                });
            });
        }
    }
});

App.Router.map(function() {
    this.resource('lunch', {path: 'lunch/:lunchId'});
});

App.IndexRoute = Ember.Route.extend(App.LunchActions, {
    model: function() {
        var deferred = $.Deferred();

        App.Api.call('lunch/available', 'GET', null, function(result) {
            deferred.resolve(result);
        }, function(error) {
            deferred.resolve([]);
            App.alertWithRequestError(error);
        });

        return deferred.promise();
    }
});

App.LunchRoute = Ember.Route.extend(App.LunchActions, {
    model: function(parameters) {
        var deferred = $.Deferred();

        App.Api.call('lunch?lunchId=' + parameters.lunchId, 'GET', null, function(result) {
            deferred.resolve(result);
        }, function(error) {
            App.alertWithRequestError(error);
        });

        return deferred.promise();
    }
});
