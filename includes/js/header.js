$(document).ready(function() {
	if ($('#error').not(':empty')) {
		$('#error').fadeIn(1000);
	}
	$('#forgot_password').click(function() {
		$('#login_process #password, #login_process #password_label, #login_process #signup').fadeToggle("fast");
		if($('#submit_btn').html() == 'Login') {
			$('#submit_btn').html("Retrieve Password").val("forgot_password");
		} else {
			$('#submit_btn').html("Login").val("login");
		}
		$('#email').focus();
	});
	$('form#login_process #signup').click(function() {
		$('#login_process #forgot_password, #login_process #password2, #login_process #password2_label, login_process #forgot_password_label').fadeToggle();
		if($('#submit_btn').html() == 'Login') {
			$('#submit_btn').html("Signup").val("signup");
			$('#signup').html("Login");
		} else {
			$('#submit_btn').html("Login").val("login");
			$('#signup').html("Signup");
		}
	});
	$('#login_process #submit_btn').click(function(e) {
		if (($(this).val() == "signup" || $(this).val() == "reset") && $('#password').val() != $('#password2').val()) {
			$("#error").html("Your passwords do not match.").fadeIn();
			$('#login_process #password_label, #login_process #password2_label').addClass("error_label");
			e.preventDefault();
		}
	});
	//  Form submit buttons will be added to callback @ AJAX.
	$("form[ajax='true'] button[ajax='true'], form[ajax='true'] input[type='submit']").click(function(event) {
		var form = $(this).closest("form[ajax='true']");
		window.post = form.serializeArray();
		window.post.push({ name: this.name, value: this.value });
		var o = {};
		var a = window.post;
		$.each(a, function() {
			if (o[this.name] !== undefined) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});
		window.post = o;
	});
	//  All AJAX forms must .preventDefault() in order to maintain HTML5 form validation.
	$("form[ajax='true']").submit(function(event) {
		event.preventDefault();
		if (event.target.checkValidity() == true) {
			var callback = $(this).attr("callback");
			if (typeof window.post === 'undefined') {
				var post = $(this).serializeArray();
			} else {
				var post = window.post;
			}
			$.ajax({
				type: "POST",
				url: "ajax.php",
				dataType: "json",
				data: post,
				success: function(data) {
					if (data !== null) {
						$.each(data, function(key, value) {
							if (key == callback) {
								eval(key+"(value)");
							}
						});
					}
				}
			});
		}
	});
	$("#back-top").hide();
	$(function () {
		$(window).scroll(function () {
			if ($(this).scrollTop() > 200) {
				$('#back-top').fadeIn('slow');
			} else {
				$('#back-top').fadeOut('slow');
			}
		});
		$('#back-top').click(function () {
			$('body,html').animate({
				scrollTop : 0
			}, 1000);
			return false;
		});
	});
});

