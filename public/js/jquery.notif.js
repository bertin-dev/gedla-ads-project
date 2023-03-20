$(function () {

    //creation de mon plugin JQuery avec le template de ma notification
    //creation de mon plugin JQuery avec le template de ma notification
    $.fn.notif = function(options){
        var options = $.extend({
            html: '    <div class="alert_notification add animated fadeInLeft {{cls}}">\n' +
                '        <div class="left1">\n' +
                '            <div class="img1" style="background-image: url({{img}});">  \n' +
                '            </div>\n' +
                '        </div>\n' +
                '        <div class="right1">\n' +
                '            <h2 class="alert_title">{{title}}</h2>\n' +
                '            <p class="alert_p">{{content}}</p>\n' +
                '        </div>\n' +
                '    </div>'
        }, options);

        //permet de garder l'objet JQuery en mémoire et permet aussi d'enchainner les arguments juste apres
        return this.each(function () {
            var $this = $(this);
            var $notifs = $('> #alert_notifications', this);
            var $notif = $(Mustache.render(options.html,
                options));

            if($notifs.length == 0){
                $notifs = $('<div id="alert_notifications"/>'
                );
                $this.append($notifs);
            }
            $notifs.append($notif);
            setTimeout(function () {
                $notif.addClass('.fadeOutLeft').delay(500).slideUp(300, function () {
                    $notif.remove();
                });
            }, 6000);
        });
    };

    //apres le click
    $('.add').click(function(e){
        e.preventDefault();
        $('body').notif({
            title: 'Mon titre',
            content: 'Mon contenu',
            img: 'imgages/success-notif.jpg',
            cls: 'success1'
        });
    });
});
