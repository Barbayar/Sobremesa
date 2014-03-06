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

App.completeModelLunch = function(lunch) {
    var me = App.getMe();
    var result = lunch;

    if (result.userId == me.userId) {
        result.isMine = true;
    } else {
        result.isMine = false;
    }

    if (result.members.findBy('userId', me.userId.toString())) {
        result.isJoined = true;
    } else {
        result.isJoined = false;
    }

    return result;
}

App.completeModelLunches = function(lunches) {
    var result = [];

    lunches.forEach(function(lunch) {
        result.push(App.completeModelLunch(lunch));
    });

    return result;
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
        var controller = this.controller;

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

            $('#numberOfPeople').slider({
                range: true,
                min: 2,
                max: 20,
                values: [controller.get('minPeople'), controller.get('maxPeople')],
                slide: function(event, ui) {
                    controller.set('minPeople', ui.values[0].toString());
                    controller.set('maxPeople', ui.values[1].toString());
                }
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
    beginTimeString: moment().startOf('hour').add('hours', 1).format(App.TimeFormat.moment),
    endTimeString: moment().startOf('hour').add('hours', 2).format(App.TimeFormat.moment),
    minPeople: '4',
    maxPeople: '8',
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

App.ApplicationRoute = Ember.Route.extend({
    model: function() {
        return App.getMe();
    },
    actions: {
        createLunch: function() {
            App.lunchForm(null, function(parameters) {
                App.Api.call('lunch', 'PUT', parameters, function(result) {
                    location.href = '#/lunch/' + result;
                    // TODO: we should find a better way to avoid modal window bug
                    location.reload('#/lunch/' + result);
                }, function(error) {
                    App.alertWithRequestError(error);
                });
            });
        },
        logout: function() {
            App.Api.call('logout', 'GET', null, function(result) {
                App.removeMe();
                location.reload();
            }, function(error) {
                App.alertWithRequestError(error);
            });
        }
    }
});

App.Router.map(function() {
    this.resource('lunch', {path: 'lunch/:lunchId'});
    this.resource('date', {path: 'date/:date'});
    this.resource('profile', {path: 'profile/:userId'});
});

App.IndexRoute = Ember.Route.extend({
    model: function() {
        var deferred = $.Deferred();

        App.Api.call('lunch/available', 'GET', null, function(result) {
            deferred.resolve({
                lunches: App.completeModelLunches(result)
            });
        }, function(error) {
            deferred.resolve([]);
            App.alertWithRequestError(error);
        });

        return deferred.promise();
    }
});

App.LunchRoute = Ember.Route.extend({
    actions: {
        edit: function() {
            var controller = this.controller;

            App.lunchForm(controller.content, function(parameters) {
                parameters.lunchId = controller.content.lunchId;

                App.Api.call('lunch', 'POST', parameters, function(result) {
                    for (var key in parameters) {
                        controller.set(key, parameters[key]);
                    }
                }, function(error) {
                    App.alertWithRequestError(error);
                });
            });
        },
        delete: function() {
            var controller = this.controller;

            App.confirm('Confirmation', 'Do you really want to delete this lunch?', function() {
                App.Api.call('lunch', 'DELETE', {lunchId: controller.content.lunchId}, function(result) {
                    location.href = '#';
                    // TODO: we should find a better way to avoid modal window bug
                    location.reload();
                }, function(error) {
                    App.alertWithRequestError(error);
                });
            });
        },
        join: function() {
            var controller = this.controller;

            App.Api.call('lunch/' + controller.content.lunchId + '/member', 'PUT', null, function(result) {
                controller.content.members.pushObject(App.getMe());
                controller.set('isJoined', true);
            }, function(error) {
                App.alertWithRequestError(error);
            });
        },
        cancel: function() {
            var controller = this.controller;

            App.confirm('Confirmation', 'Do you really want to cancel joining this lunch?', function() {
                App.Api.call('lunch/' + controller.content.lunchId + '/member', 'DELETE', null, function(result) {
                    var me = controller.content.members.findBy('userId', App.getMe().userId.toString());

                    controller.content.members.removeObject(me);
                    controller.set('isJoined', false);
                }, function(error) {
                    App.alertWithRequestError(error);
                });
            });
        },
        addComment: function() {
            var controller = this.controller;

            if (!controller.content.commentToAdd) {
                return;
            }

            App.Api.call('lunch/' + controller.content.lunchId + '/comment', 'PUT', {content: controller.content.commentToAdd}, function(result) {
                var comment = App.getMe();

                comment.commentId = result;
                comment.content = controller.content.commentToAdd;
                comment.isMine = true;

                controller.content.comments.pushObject(comment);
                controller.set('commentToAdd', '');
            }, function(error) {
                App.alertWithRequestError(error);
            });
        },
        deleteComment: function(comment) {
            var controller = this.controller;

            App.confirm('Confirmation', 'Do you really want to delete this comment?', function() {
                App.Api.call('lunch/' + controller.content.lunchId + '/comment', 'DELETE', {commentId: comment.commentId}, function(result) {
                    controller.content.comments.removeObject(comment);
                }, function(error) {
                    App.alertWithRequestError(error);
                });
            });
        }
    },
    model: function(parameters) {
        var deferred = $.Deferred();

        App.Api.call('lunch', 'GET', {lunchId: parameters.lunchId}, function(result) {
            var me = App.getMe();

            result.comments.forEach(function(item) {
                if (item.userId == me.userId) {
                    item.isMine = true;

                    return;
                };

                item.isMine = false;
            });

            deferred.resolve(App.completeModelLunch(result));
        }, function(error) {
            App.alertWithRequestError(error);
        });

        return deferred.promise();
    }
});

App.DateRoute = Ember.Route.extend({
    model: function(parameters) {
        var deferred = $.Deferred();

        App.Api.call('lunch/byDate', 'GET', {date: parameters.date}, function(result) {
            deferred.resolve({
                date: parameters.date.substring(0, 4) + '.' + parameters.date.substring(4, 6) + '.' + parameters.date.substring(6, 8),
                lunches: App.completeModelLunches(result)
            });
        }, function(error) {
            App.alertWithRequestError(error);
        });

        return deferred.promise();
    }
});

App.ProfileRoute = Ember.Route.extend({
    model: function(parameters) {
        var deferred = $.Deferred();

        // TODO: will use parameters.lunchId
        var user = App.getMe();

        App.Api.call('lunch/byCreatorId', 'GET', {userId: user.userId}, function(createdLunches) {
            App.Api.call('lunch/joined', 'GET', null, function(joinedLunches) {
                var lunches = createdLunches.concat(joinedLunches).sortBy('beginTime');

                deferred.resolve({
                    user: user,
                    lunches: App.completeModelLunches(lunches)
                });
            }, function(error) {
                App.alertWithRequestError(error);
            });
        }, function(error) {
            App.alertWithRequestError(error);
        });

        return deferred.promise();
    }
});

App.ApplicationView = Ember.View.extend({
    didInsertElement : function(){
        this._super();
        $("#calendar").datepicker({
            dateFormat: 'yymmdd',
            onSelect: function(date) {
                location.href = "#/date/" + date;
            }
        });
    }
});
