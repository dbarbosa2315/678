var system = require('system');

var url = atob(system.args[1]), file = atob(system.args[2]);

var page = require('webpage').create();

page.paperSize = { width: '80mm', height: '297mm', margin: '0' };

page.onLoadFinished = function (status) {

    console.log(file);

    page.render(file);
    
    phantom.exit();
};

page.open(url);