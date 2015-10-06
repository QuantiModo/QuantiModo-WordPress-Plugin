jQuery(document).ready(function () {

    Quantimodo.getCurrentUser(function (user) {
        console.debug(user);

        window.intercomSettings = {
            app_id: "wsj1w4xj",
            name: user.displayName,
            email: user.email,
            created_at: new Date(user.userRegistered) / 1000
        };

        (function () {
            var w = window;
            var ic = w.Intercom;
            if (typeof ic === "function") {
                ic('reattach_activator');
                ic('update', intercomSettings);
            } else {
                var d = document;
                var i = function () {
                    i.c(arguments);
                };
                i.q = [];
                i.c = function (args) {
                    i.q.push(args);
                };
                w.Intercom = i;
                function l() {
                    var s = d.createElement('script');
                    s.type = 'text/javascript';
                    s.async = true;
                    s.src = 'https://widget.intercom.io/widget/wsj1w4xj';
                    var x = d.getElementsByTagName('script')[0];
                    x.parentNode.insertBefore(s, x);
                }

                l();

            }
        })();

    });


});