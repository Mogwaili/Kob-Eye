( function(a, b) {
		a.toastr = (function() {
			var h = {
				tapToDismiss : true,
				toastClass : "toast",
				containerId : "toast-container",
				debug : false,
				fadeIn : 300,
				fadeOut : 1000,
				extendedTimeOut : 1000,
				iconClasses : {
					error : "toast-error",
					info : "toast-info",
					success : "toast-success",
					warning : "toast-warning"
				},
				iconClass : "toast-info",
				positionClass : "toast-top-right",
				timeOut : 5000,
				titleClass : "toast-title",
				messageClass : "toast-message"
			}, c = function(k, l) {
				//play_sound_message_box();
				return d({
					iconClass : e().iconClasses.error,
					message : k,
					title : l
				})
			}, j = function(k) {
				var l = b("#" + k.containerId);
				if (l.length) {
					return l
				}
				l = b("<div/>").attr("id", k.containerId).addClass(k.positionClass);
				l.appendTo(b("body"));
				return l
			}, e = function() {
				return b.extend({}, h, toastr.options)
			}, g = function(k, l) {
				//popsound();
				return d({
					iconClass : e().iconClasses.info,
					message : k,
					title : l
				})
			}, d = function(l) {
				var v = e(), s = l.iconClass || v.iconClass, t = null, u = j(v), p = b("<div/>"), r = b("<div/>"), k = b("<div/>"), q = {
					options : v,
					map : l
				};
				if (l.iconClass) {
					p.addClass(v.toastClass).addClass(s)
				}
				if (l.title) {
					r.append(l.title).addClass(v.titleClass);
					p.append(r)
				}
				if (l.message) {
					k.append(l.message).addClass(v.messageClass);
					p.append(k)
				}
				var n = function() {
					if (b(":focus", p).length > 0) {
						return
					}
					var w = function() {
						return p.fadeOut(v.fadeOut)
					};
					b.when(w()).done(function() {
						if (p.is(":visible")) {
							return
						}
						p.remove();
						if (u.children().length === 0) {
							u.remove()
						}
					})
				};
				var o = function() {
					if (v.timeOut > 0 || v.extendedTimeOut > 0) {
						t = setTimeout(n, v.extendedTimeOut)
					}
				};
				var m = function() {
					clearTimeout(t);
					p.stop(true, true).fadeIn(v.fadeIn)
				};
				p.hide();
				u.prepend(p);
				p.fadeIn(v.fadeIn);
				if (v.timeOut > 0) {
					t = setTimeout(n, v.timeOut)
				}
				p.hover(m, o);
				if (v.tapToDismiss) {
					p.click(n)
				}
				if (v.debug) {
					console.log(q)
				}
				return p
			}, i = function(k, l) {
				//play_sound_big_box();
				return d({
					iconClass : e().iconClasses.success,
					message : k,
					title : l
				})
			}, f = function(k, l) {
				//popsound();
				return d({
					iconClass : e().iconClasses.warning,
					message : k,
					title : l
				})
			};
			return {
				error : c,
				info : g,
				options : {},
				success : i,
				warning : f
			}
		})()
	}(window, jQuery)); 