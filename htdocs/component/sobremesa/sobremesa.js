var Sobremesa = {
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
    }
}
