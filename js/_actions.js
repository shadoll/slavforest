/**
 *
 * @author sHa <sha@shadoll.com>
 * @package sCore
 * @version 17.5.9 (17.5.16 fixed)
 *
 */
;

/* AutoLoader */
jQuery(document).ready(function(){
	jQuery("[data-autoload='true']").each(function(){
		load_objects(jQuery("#"+$(this).attr("id")));
	});
});

/*
	Виклик:
		load_objects(jQuery("#container_id"));
	Параметри data- контейнеру:
		* - будь-які данні, будуть передані в викликаємий метод класу
		action - клас та метод що маємо викликати, якщо не задано берем з href
		method - метод Post або Get
		callback - функція яку буде викона після виконання запиту
		target - якщо вказано, то значення змінної html відповіді буде розміщенно всередені тегу
		якщо target не визначено, а відповідь має змінну html, то її значення буде розміщено в середені контейнеру
		template - ім'я php шаблону для формування змінної html
		autoload - true|false - автоматичне виконання запиту при завантаженні сторінки
*/

var load_objects = function(container){
	if(jQuery(container)!=undefined){

		var action = container.data('action');
		if (action==undefined)
			action = container.attr('href');

		var method = container.data('method');
		if(method==undefined)
			method = 'GET';

		// Отримуємо необхідну форму,
		// якщо не знайшли, беремо всі форми
		var form_data = jQuery("form[data-group='"+container.attr('id')+"']").serializeArray();
// 		if (form_data[0] == undefined)
// 			var form_data = jQuery('form').serializeArray();

		// перетворюємо значення форми на об'ект data
		var data = new Object();
		jQuery.each(form_data, function() {
			if(this.value!='')
				data[this.name] = this.value;
		});

		// додаємо до об'єкту data значання data- полів контейнеру
		jQuery.each(container.data(),function(n, v){
			if(n!='action' && n!='callback')
				data[n] = v;
		});

		// додамо поточний url (хай буде)
		data["uri_source"] = window.location.href;

		jQuery.ajax({
			url: action,
			type: method,
			data: data
		}).
		done(function(data){
			var response = jQuery.parseJSON(data);

			var target = jQuery(response.target);
			console.log(target);
			if(target!=undefined)
				jQuery(target).html(response.html);
			else
				container.html(response.html);

			if(response.callback!==undefined){
				callback = response.callback;
			}
			else if(container.data('callback')!==undefined){
				callback = container.data('callback');
			}
			else{
				callback = undefined;
			}
			if(callback!==undefined && typeof window[callback] == 'function') {
				window[callback](container,response);
			}
		}).
		fail(function( jqXHR, textStatus ){
			container.html("Request failed: " + textStatus);
		});
	}
}
