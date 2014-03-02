App = Ember.Application.create();

App.TimeFormat = {
    datetimepicker: 'yyyy.mm.dd hh:ii',
    moment: 'YYYY.MM.DD HH:mm'
};

App.Api = {
    call: function(uri, type, parameters, callback, callbackOnError) {
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
            if (jqXHR.status == '401' && redirect == true) {
                window.location.href = 'login.html';

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
            this.callback(this.parameter);
        }
    }
});

App.confirm = function(title, message, callback, parameter) {
    /*
        TODO: it throws 'DEPRECATION' error, but still working
        we should fix this to not throw error
    */
    App.ConfirmView.create().set('controller', App.ConfirmController.create({
        title: title,
        message: message,
        callback: callback,
        parameter: parameter,
    })).appendTo('body');
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

            if (this.lunchId) {
                // edit mode
                view.$().modal('hide');
            }
        }
    }
});

App.LunchActions = Ember.Mixin.create({
    actions: {
        edit: function(model) {
            this.container.lookup('view:lunchForm').set('controller', App.LunchFormController.create({
                lunchId: model.lunchId,
                theme: model.theme,
                location: model.location,
                description: model.description,
                beginTime: model.beginTime,
                endTime: model.endTime,
                beginTimeString: moment.unix(model.beginTime).format(App.TimeFormat.moment),
                endTimeString: moment.unix(model.endTime).format(App.TimeFormat.moment),
                minPeople: model.minPeople,
                maxPeople: model.maxPeople
            })).append();
        }
    }
});

App.ApplicationRoute = Ember.Route.extend({
    actions: {
        createLunch: function() {
            this.container.lookup('view:lunchForm').set('controller', App.LunchFormController.create({
                formTitle: 'Create'
            })).append();
        }
    }
});

App.Router.map(function() {
    this.resource('login');
    this.resource('lunch', {path: 'lunch/:lunchId'});
});

App.IndexRoute = Ember.Route.extend(App.LunchActions, {
    model: function() {
        var deferred = $.Deferred();

        App.Api.call('lunch/available', 'GET', null, function(result) {
            deferred.resolve(result);
        }, function(error) {
            deferred.resolve([]);

            App.alert('Error', 'An unknown error occurred');
        });

        return deferred.promise();
    }
});

App.LunchRoute = Ember.Route.extend(App.LunchActions, {
    model: function(params) {
        var deferred = $.Deferred();

        App.Api.call('lunch?lunchId=' + params.lunchId, 'GET', null, function(result) {
            deferred.resolve(result);
        }, function(error) {
            deferred.resolve({});

            if (error.status == '400') {
                App.alert('Error', 'Invalid parameters');
            } else if (error.status == '404') {
                App.alert('Error', 'Couldn\'t find the lunch');
            } else {
                App.alert('Error', 'An unknown error occurred');
            }
        });

        return deferred.promise();
    }
});

App.LoginRoute = Ember.Route.extend({
    model: function() {
        return null;
    }
});
