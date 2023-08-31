var templates = new Array('index', 'collect', 'microsoft', 'winner', 'sign-up', 'a1', 'a2');
var members;

$(document).ready(function() {


	var temp_name;
	var hash = location.hash.replace('/', '');
	var id = hash.slice(1);

	if (id == 'a1' || id == 'a2')
	{
		viewRender('collect')
		location.hash = '#'+id;
		pushUrl(id);


	} else if(templates.indexOf(id) != -1) {
		viewRender(id)
		pushUrl(id);
	} else {

		viewRender('index');
		pushUrl('index');
	}

	$('#navMenu li a').click(function(event) {
		event.preventDefault();
		temp_name = $(this).attr('data-temp-name');
		viewRender(temp_name);
		pushUrl(temp_name);

	});
	$('#MAIN').delegate('button[data-temp-name]', 'click', function(event) {
		event.preventDefault();
		temp_name = $(this).attr('data-temp-name');
		viewRender(temp_name);
		pushUrl(temp_name);

	});
	$(window).bind("popstate", function (event){
		var temp_name;
		var hash = location.hash.replace('/', '');
		var id = hash.slice(1);

		viewRender(id);
		$('#navMenu li a').removeClass('active');
		$('a[data-temp-name="'+id+'"]').addClass('active');
		// $('#MAIN').html(twig({
		// 	href: "templates/" + id + ".twig",
		// 	async: false
		// }).render());
		// if (id == 'a1' || id == 'a2')
		// {
		// 	viewRender('collect')
		// 	location.hash = '#'+id;


		// } else if(templates.indexOf(id) != -1) {
		// 	viewRender(id)
		// } else {

		// 	viewRender('index');
		// }
	})

});

function viewRender (template_name) {

	// if (template_name === 'sign-up') {
	// 	jQuery.ajax({
	// 	  url: 'action.php',
	// 	  type: 'POST',
	// 	  dataType: 'json',
	// 	  data: {action: 'getParticipant'},
	// 	  success: function(data, textStatus, xhr) {
	// 	  	temp = data.data;
	// 		$('#MAIN').html(twig({
	// 			href: "templates/" + template_name + ".twig",
	// 			async: false
	// 		}).render({data:temp}));
	// 	  }
	// 	});
	// } else if (template_name === 'collect') {
	// 	var member;
	// 	var dataObject = {
	// 		action: 'checkLogin',
	// 	};
	// 	sendAjax(dataObject, function(data) {
	// 		member = data.data.data;
	// 		$('#MAIN').html(twig({
	// 			href: "templates/" + template_name + ".twig",
	// 			async: false
	// 		}).render({member:member}));
	// 	})
	// }else {
		$('#MAIN').html(twig({
			href: "templates/" + template_name + ".twig",
			async: false
		}).render());
	// }


}

function pushUrl(template_name){
	var url = '#/' + template_name;
	console.log(url);
	window.history.pushState('', '', url );
	$('#navMenu li a').removeClass('active');
	$('a[data-temp-name="'+template_name+'"]').addClass('active');
}


// function checkLogin() {
// 	var dataObject = {
// 		action: 'checkLogin',
// 	};

// 	jQuery.ajax({
// 		url: 'http://event2.muzik-online.com/function.php',
// 		xhrFields: {
// 			withCredentials: true
// 		},
// 		type: 'POST',
// 		dataType: 'json',
// 		data: {
// 			type: 'login'
// 		},
// 		complete: function(xhr, textStatus) {
// 			//called when complete
// 		},
// 		success: function(data, textStatus, xhr) {
// 			console.log(data);
// 		},
// 		error: function(xhr, textStatus, errorThrown) {
// 			//called when there is an error
// 		}
// 	});

// }
