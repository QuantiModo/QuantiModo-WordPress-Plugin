jQuery(document).ready(function () {

    Quantimodo.getCurrentUser(function (user) {

        if (user && typeof window.intercomSettings !== 'undefined') {
            window.intercomSettings.name = user.displayName;
            window.intercomSettings.email = user.email;
            window.intercomSettings.created_at = new Date(user.userRegistered) / 1000;
        }

    });

});