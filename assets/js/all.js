function reloadTable() {
	$('.table-container .loading').show();
	$.ajax({
		url: '../ajax/s.php?query=request_contacts',
		type: 'GET',
		dataType: 'json',
		timeout: 25000,
		success: function(data, textStatus, xhr) {
			var raw_str = '';
			var nodes = data.data;
			for (var i = 0; i < nodes.length; i++) {
				raw_str += '<tr>';
				raw_str += '	<td>' + nodes[i].name + '</td>';
				raw_str += '	<td>' + nodes[i].phone + '</td>';
				raw_str += '	<td>' + nodes[i].email + '</td>';
				raw_str += '	<td>' + nodes[i].qq + '</td>';
				raw_str += '	<td>' + nodes[i].major + '</td>';
				raw_str += '</tr>';
			}
			$('tbody.nodes').html(raw_str);
			$('.table-container .loading').hide();
		},
		error: function(xhr, textStatus, errorThrown) {
			location.reload();
		}
	});
}

function animate(status) {
	if (status === 'pre_animation') {
		$('.table-container .loading').hide();
		$('.table-container').hide();
		$('.animation .animate1').hide();
		$('.animation .animate2').hide();
		$('.animation .animate3').hide();
		$('.animation .animate4').hide();
		$('.animation .animate4').animate();
	} else if (status === 'animate') {
		$('.table-container .loading').show();
		$('.animation .animate1').delay(500).fadeIn(1000);
		$('.animation .animate2').delay(1500).fadeIn(800);
		$('.animation .animate3').delay(2300).fadeIn(800);
		$('.animation .animate4').delay(3000).fadeIn(1000);
		$('.table-container').delay(3500).fadeIn(1000);
	}
}

$(function(){
	animate('pre_animation');
	animate('animate');
	reloadTable();
	/**
	 * Process the register form.
	 */
	$('#contact-form').submit(function(){
		$('.form-success').html('');
		var urlString = "../ajax/s.php";
		var inputs = new Array ("ELEMENTS", "name", "phone", "email", "qq", "role", "major", "query");
		var elements = new Array ("name", "phone", "email", "qq", "role", "major");

		formProcess ("#contact-form", false, urlString, inputs, function() {
				reloadTable();
				clearInputs('#contact-form', elements);
				$('.form-success').html('已成功添加！');
			});

		return false;
	});
});