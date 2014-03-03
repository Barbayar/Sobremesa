Ember.Handlebars.helper('readableTime', function(timestamp) {
    return moment.unix(timestamp).fromNow();
});

Ember.Handlebars.helper('stringTime', function(timestamp) {
    return moment.unix(timestamp).format(App.TimeFormat.moment);
});

Ember.Handlebars.helper('avatarURL', function(data, size) {
    var dataObject = JSON.parse(data);
    var avatarURL = dataObject.gravatarURL;

    if (size) {
        avatarURL += '?s=' + size;
    }

    return avatarURL;
});

Ember.Handlebars.helper('googleMapURL', function(location) {
    return 'https://maps.google.com/?q=' + encodeURIComponent(location);
});

Handlebars.registerHelper('isMine', function(conditional, options) {
    var userId = Ember.Handlebars.get(this, conditional);
    var me = App.getMe();

    if (userId == me.userId) {
        return options.fn(this);
    }

    return options.inverse(this);
});

Handlebars.registerHelper('isJoined', function(conditional, options) {
    var members = Ember.Handlebars.get(this, conditional);
    var me = App.getMe();

    for (var i = 0; i < members.length; i++) {
        if (members[i].userId == me.userId) {
            return options.fn(this);
        }
    }

    return options.inverse(this);
});
