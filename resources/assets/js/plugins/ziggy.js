    var Ziggy = {
        namedRoutes: {"admin.login":{"uri":"admin","methods":["GET","HEAD"],"domain":null},"admin.login.post":{"uri":"admin\/login","methods":["POST"],"domain":null},"admin.logout":{"uri":"admin\/logout","methods":["POST"],"domain":null},"register":{"uri":"admin\/register","methods":["GET","HEAD"],"domain":null},"password.request":{"uri":"admin\/password\/reset","methods":["GET","HEAD"],"domain":null},"password.email":{"uri":"admin\/password\/email","methods":["POST"],"domain":null},"password.reset":{"uri":"admin\/password\/reset\/{token}","methods":["GET","HEAD"],"domain":null},"admin.dashboard":{"uri":"admin\/dashboard","methods":["GET","HEAD"],"domain":null},"admin.user.index":{"uri":"admin\/users","methods":["GET","HEAD"],"domain":null}},
        baseUrl: 'http://laravel.lti.local/',
        baseProtocol: 'http',
        baseDomain: 'laravel.lti.local',
        basePort: false
    };

    export {
        Ziggy
    }
