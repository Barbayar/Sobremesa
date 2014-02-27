String.prototype.lpad = function(length) {
    var str = this;

    while (str.length < length) {
        str = '0' + str;
    }

    return str;
}

var Sobremesa = {
    dateFormat: 'yyyy.mm.dd hh:ii',
    callApi: function(uri, type, parameters, callback, callbackOnError, redirect) {
        redirect = typeof redirect !== 'undefined' ? redirect : true;
        var url = 'api/v1/' + uri;

        if (parameters) {
            url += '?' + $.param(parameters);
        }

        Sobremesa.loading(true);
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
            Sobremesa.loading(false);
        });
    },
    setMe: function(me) {
        $.cookie.json = true;
        $.cookie('me', me);
    },
    getMe: function() {
        $.cookie.json = true;
        return $.cookie('me');
    },
    removeMe: function() {
        $.removeCookie('me');
    },
    getAvatarURL: function(user, size) {
        var data = JSON.parse(user.data);
        var avatarURL = data.gravatarURL;

        if (size) {
            avatarURL += '?s=' + size;
        }

        return avatarURL;
    },
    stringToTimestamp: function(string) {
        var date = new Date(string);

        return date.getTime() / 1000;
    },
    timestampToString: function(timestamp) {
        var date = new Date(timestamp * 1000);
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        var day = date.getDate();
        var hours = date.getHours();
        var minutes = date.getMinutes();

        return year + '.' + month.toString().lpad(2) + '.' + day.toString().lpad(2) + ' ' + hours.toString().lpad(2) + ':' + minutes.toString().lpad(2);
    },
    timestampToReadableString: function(timestamp) {
        var date = new Date();
        var timeDifference = Math.abs(date.getTime() / 1000 - timestamp);

        if (timeDifference < 60) {
            return 'now';
        }

        if (timeDifference / 60 < 60) {
            var minutes = Math.floor(timeDifference / 60);

            if (minutes != 1) {
                return  minutes + ' minutes';
            }

            return 'a minute';
        }

        if (timeDifference / 3600 < 24) {
            var hours = Math.floor(timeDifference / 3600);

            if (hours != 1) {
                return hours + ' hours';
            }

            return 'a hour';
        }

        var days = Math.floor(timeDifference / 86400);

        if (days != 1) {
            return  days + ' days';
        }

        return 'a day';
    },
    alert: function(title, message) {
        if ($('#alertWindow').size() == 0) {
            $("body").append($(' \
                <div class="modal fade" id="alertWindow" tabindex="-1" role="dialog" aria-labelledby="alertWindowTitle" aria-hidden="true" style="display: none;"> \
                    <div class="modal-dialog"> \
                        <div class="modal-content"> \
                            <div class="modal-header"> \
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> \
                                <h4 class="modal-title" id="alertWindowTitle">Title</h4> \
                            </div> \
                            <div class="modal-body"> \
                                <label id="alertWindowMessage">Message</label> \
                            </div> \
                            <div class="modal-footer"> \
                                <button type="button" class="btn btn-danger" data-dismiss="modal">OK</button> \
                            </div> \
                        </div> \
                    </div> \
                </div> \
            '));
        }

        $('#alertWindowTitle').html(title);
        $('#alertWindowMessage').html(message);
        $('#alertWindow').modal('show');
    },
    confirm: function(title, message, callback, parameter) {
        if ($('#confirmWindow').size() == 0) {
            $("body").append($(' \
                <div class="modal fade" id="confirmWindow" tabindex="-1" role="dialog" aria-labelledby="confirmWindowTitle" aria-hidden="true" style="display: none;"> \
                    <div class="modal-dialog"> \
                        <div class="modal-content"> \
                            <div class="modal-header"> \
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> \
                                <h4 class="modal-title" id="confirmWindowTitle">Title</h4> \
                            </div> \
                            <div class="modal-body"> \
                                <label id="confirmWindowMessage">Message</label> \
                            </div> \
                            <div class="modal-footer"> \
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> \
                                <button type="button" class="btn btn-primary" id="confirmWindowOKButton">OK</button> \
                            </div> \
                        </div> \
                    </div> \
                </div> \
            '));
        }

        $('#confirmWindowTitle').html(title);
        $('#confirmWindowMessage').html(message);
        $('#confirmWindowOKButton').unbind('click');
        $('#confirmWindowOKButton').click(function(event) {
            callback(parameter);
            $('#confirmWindow').modal('hide');
        });
        $('#confirmWindow').modal('show');
    },
    loading: function(show) {
        if ($('#loadingWindow').size() == 0) {
            $("body").append($(' \
                <div class="modal fade" id="loadingWindow" tabindex="-1" role="dialog" aria-labelledby="loadingWindowTitle" aria-hidden="true" style="display: none;"> \
                    <div class="modal-dialog"> \
                        <div class="modal-content"> \
                            <div class="modal-header"> \
                                <h4 class="modal-title" id="loadingWindowTitle">Loading...</h4> \
                            </div> \
                            <div class="modal-body"> \
                                <div class="progress progress-striped active"> \
                                    <div class="progress-bar progress-bar-info" role="progressbar" style="width: 100%"> \
                                    </div> \
                                </div> \
                            </div> \
                        </div> \
                    </div> \
                </div> \
            '));
        }

        if (show) {
            $('#loadingWindow').modal({
                backdrop: 'static',
                keyboard: false
            });
        } else {
            $('#loadingWindow').modal('hide');
        }
    }
}
