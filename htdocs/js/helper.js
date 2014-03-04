Ember.Handlebars.helper('readableTime', function(timestamp, options) {
    return moment.unix(timestamp).fromNow();
});

Ember.Handlebars.helper('stringTime', function(timestamp, options) {
    return moment.unix(timestamp).format(App.TimeFormat.moment);
});

Ember.Handlebars.helper('avatarURL', function(data, size, options) {
    var dataObject = JSON.parse(data);
    var avatarURL = dataObject.gravatarURL;

    if (size) {
        avatarURL += '?s=' + size;
    }

    return avatarURL;
});

Ember.Handlebars.helper('googleMapURL', function(location, options) {
    return 'https://maps.google.com/?q=' + encodeURIComponent(location);
});

Ember.Handlebars.helper('breaklines', function(text, options) {
    text = Handlebars.Utils.escapeExpression(text);
    text = text.replace(/(\r\n|\n|\r)/gm, '<br>');
    return new Handlebars.SafeString(text);
});

Ember.Handlebars.helper('timelineClass', function(dummy, options) {
    var index = options.data.view.contentIndex;

    if (index % 2 === 0) {
        return '';
    }

    return 'timeline-inverted';
});
