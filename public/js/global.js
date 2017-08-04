window.getQueryFromURL = function (url) {
	url = url || 'http://dankegongyu.com/s?a=b#rd'; // 做一层保护，保证URL是合法的
	var query = url.split('?')[1].split('#')[0].split('&'),
		params = {};
	for (var i = 0; i < query.length; i++) {
		var arg = query[i].split('=');
		params[arg[0]] = arg[1];
	}
	return params;
};
